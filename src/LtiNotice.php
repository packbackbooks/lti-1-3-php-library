<?php

namespace Packback\Lti1p3;

use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use GuzzleHttp\Exception\TransferException;
use Packback\Lti1p3\Interfaces\ICache;
use Packback\Lti1p3\Interfaces\ICookie;
use Packback\Lti1p3\Interfaces\IDatabase;
use Packback\Lti1p3\Interfaces\ILtiDeployment;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\MessageValidators\NoticeMessageValidator;

class LtiNotice
{
    public const ERR_FETCH_PUBLIC_KEY = 'Failed to fetch public key.';
    public const ERR_NO_PUBLIC_KEY = 'Unable to find public key.';
    public const ERR_NO_MATCHING_PUBLIC_KEY = 'Unable to find a public key which matches your JWT.';
    public const ERR_STATE_NOT_FOUND = 'Please make sure you have cookies and cross-site tracking enabled in the privacy and security settings of your browser.';
    public const ERR_MISSING_ID_TOKEN = 'Missing id_token.';
    public const ERR_INVALID_ID_TOKEN = 'Invalid id_token, JWT must contain 3 parts.';
    public const ERR_MISSING_NONCE = 'Missing Nonce.';
    public const ERR_INVALID_NONCE = 'Invalid Nonce.';

    /**
     * :issuerUrl and :clientId are used to substitute the queried issuerUrl
     * and clientId. Do not change those substrings without changing how the
     * error message is built.
     */
    public const ERR_MISSING_REGISTRATION = 'LTI 1.3 Registration not found for Issuer :issuerUrl and Client ID :clientId. Please make sure the LMS has provided the right information, and that the LMS has been registered correctly in the tool.';
    public const ERR_CLIENT_NOT_REGISTERED = 'Client id not registered for this issuer.';
    public const ERR_NO_KID = 'No KID specified in the JWT Header.';
    public const ERR_INVALID_SIGNATURE = 'Invalid signature on id_token';
    public const ERR_MISSING_DEPLOYEMENT_ID = 'No deployment ID was specified';
    public const ERR_NO_DEPLOYMENT = 'Unable to find deployment.';
    public const ERR_INVALID_MESSAGE_TYPE = 'Invalid message type';
    public const ERR_UNRECOGNIZED_MESSAGE_TYPE = 'Unrecognized message type.';
    public const ERR_INVALID_MESSAGE = 'Message validation failed.';
    public const ERR_INVALID_ALG = 'Invalid alg was specified in the JWT header.';
    public const ERR_MISMATCHED_ALG_KEY = 'The alg specified in the JWT header is incompatible with the JWK key type.';
    public const ERR_OAUTH_KEY_SIGN_NOT_VERIFIED = 'Unable to upgrade from LTI 1.1 to 1.3. No OAuth Consumer Key matched this signature.';
    public const ERR_OAUTH_KEY_SIGN_MISSING = 'Unable to upgrade from LTI 1.1 to 1.3. The oauth_consumer_key_sign was not provided.';
    private array $request;
    private array $jwt;
    private ?ILtiRegistration $registration;
    private ?ILtiDeployment $deployment;

    // See https://www.imsglobal.org/spec/security/v1p1#approved-jwt-signing-algorithms.
    private static $ltiSupportedAlgs = [
        'RS256' => 'RSA',
        'RS384' => 'RSA',
        'RS512' => 'RSA',
        'ES256' => 'EC',
        'ES384' => 'EC',
        'ES512' => 'EC',
    ];

    public function __construct(
        private IDatabase $db,
        private ICache $cache,
        private ICookie $cookie,
        private ILtiServiceConnector $serviceConnector
    ) {}

    /**
     * Static function to allow for method chaining without having to assign to a variable first.
     */
    public static function new(
        IDatabase $db,
        ICache $cache,
        ICookie $cookie,
        ILtiServiceConnector $serviceConnector
    ): self {
        return new LtiMessageLaunch($db, $cache, $cookie, $serviceConnector);
    }

    public function setRequest(array $request): self
    {
        $this->request = $request;

        return $this;
    }

    public function initialize(array $request): self
    {
        return $this->setRequest($request)
            ->validate();
    }

    /**
     * Validates all aspects of an incoming LTI message launch and caches the launch if successful.
     *
     * @throws LtiException Will throw an LtiException if validation fails
     */
    public function validate(): self
    {
        return $this->validateJwtFormat()
            ->validateNonce()
            ->validateRegistration()
            ->validateJwtSignature()
            ->validateDeployment()
            ->validateMessage();
    }

    /**
     * Fetches the decoded body of the JWT used in the current message.
     */
    public function getBody(): array
    {
        return $this->jwt['body'];
    }

    public static function getMissingRegistrationErrorMsg(string $issuerUrl, ?string $clientId = null): string
    {
        // Guard against client ID being null
        if (!isset($clientId)) {
            $clientId = '(N/A)';
        }

        $search = [':issuerUrl', ':clientId'];
        $replace = [$issuerUrl, $clientId];

        return str_replace($search, $replace, static::ERR_MISSING_REGISTRATION);
    }

    /**
     * @throws LtiException
     */
    private function getPublicKey(): Key
    {
        $request = new ServiceRequest(
            ServiceRequest::METHOD_GET,
            $this->registration->getKeySetUrl(),
            ServiceRequest::TYPE_GET_KEYSET
        );

        // Download key set
        try {
            $response = $this->serviceConnector->makeRequest($request);
        } catch (TransferException $e) {
            throw new LtiException(static::ERR_NO_PUBLIC_KEY, previous: $e);
        }
        $publicKeySet = $this->serviceConnector->getResponseBody($response);

        if (empty($publicKeySet)) {
            // Failed to fetch public keyset from URL.
            throw new LtiException(static::ERR_FETCH_PUBLIC_KEY);
        }

        // Find key used to sign the JWT (matches the KID in the header)
        foreach ($publicKeySet['keys'] as $key) {
            if ($key['kid'] == $this->jwt['header']['kid']) {
                $key['alg'] = $this->getKeyAlgorithm($key);

                try {
                    $keySet = JWK::parseKeySet([
                        'keys' => [$key],
                    ]);
                } catch (Exception $e) {
                    // Do nothing
                }

                if (isset($keySet[$key['kid']])) {
                    return $keySet[$key['kid']];
                }
            }
        }

        // Could not find public key with a matching kid and alg.
        throw new LtiException(static::ERR_NO_MATCHING_PUBLIC_KEY);
    }

    /**
     * If alg is omitted from the JWK, infer it from the JWT header alg.
     * See https://datatracker.ietf.org/doc/html/rfc7517#section-4.4.
     */
    private function getKeyAlgorithm(array $key): string
    {
        if (isset($key['alg'])) {
            return $key['alg'];
        }

        // The header alg must match the key type (family) specified in the JWK's kty.
        if ($this->jwtAlgMatchesJwkKty($key)) {
            return $this->jwt['header']['alg'];
        }

        throw new LtiException(static::ERR_MISMATCHED_ALG_KEY);
    }

    private function jwtAlgMatchesJwkKty(array $key): bool
    {
        $jwtAlg = $this->jwt['header']['alg'];

        return isset(self::$ltiSupportedAlgs[$jwtAlg]) &&
            self::$ltiSupportedAlgs[$jwtAlg] === $key['kty'];
    }

    protected function validateJwtFormat(): self
    {
        if (!isset($this->request['id_token'])) {
            throw new LtiException(static::ERR_MISSING_ID_TOKEN);
        }

        // Get parts of JWT.
        $jwt_parts = explode('.', $this->request['id_token']);

        if (count($jwt_parts) !== 3) {
            // Invalid number of parts in JWT.
            throw new LtiException(static::ERR_INVALID_ID_TOKEN);
        }

        // Decode JWT headers.
        $this->jwt['header'] = json_decode(JWT::urlsafeB64Decode($jwt_parts[0]), true);
        // Decode JWT Body.
        $this->jwt['body'] = json_decode(JWT::urlsafeB64Decode($jwt_parts[1]), true);

        return $this;
    }

    protected function validateNonce(): self
    {
        if (!isset($this->jwt['body']['nonce'])) {
            throw new LtiException(static::ERR_MISSING_NONCE);
        }
        if (!$this->cache->checkNonceIsValid($this->jwt['body']['nonce'], $this->request['state'])) {
            throw new LtiException(static::ERR_INVALID_NONCE);
        }

        return $this;
    }

    protected function validateRegistration(): self
    {
        // Find registration.
        $clientId = $this->getAud();
        $issuerUrl = $this->jwt['body']['iss'];
        $this->registration = $this->db->findRegistrationByIssuer($issuerUrl, $clientId);

        if (!isset($this->registration)) {
            throw new LtiException($this->getMissingRegistrationErrorMsg($issuerUrl, $clientId));
        }

        // Check client id.
        if ($clientId !== $this->registration->getClientId()) {
            // Client not registered.
            throw new LtiException(static::ERR_CLIENT_NOT_REGISTERED);
        }

        return $this;
    }

    protected function validateJwtSignature(): self
    {
        if (!isset($this->jwt['header']['kid'])) {
            throw new LtiException(static::ERR_NO_KID);
        }

        // Fetch public key.
        $public_key = $this->getPublicKey();

        // Validate JWT signature
        try {
            $headers = new \stdClass;
            JWT::decode($this->request['id_token'], $public_key, $headers);
        } catch (ExpiredException $e) {
            // Error validating signature.
            throw new LtiException(static::ERR_INVALID_SIGNATURE, previous: $e);
        }

        return $this;
    }

    protected function validateDeployment(): self
    {
        if (!isset($this->jwt['body'][LtiConstants::DEPLOYMENT_ID])) {
            throw new LtiException(static::ERR_MISSING_DEPLOYEMENT_ID);
        }

        // Find deployment.
        $client_id = $this->getAud();
        $this->deployment = $this->db->findDeployment($this->jwt['body']['iss'], $this->jwt['body'][LtiConstants::DEPLOYMENT_ID], $client_id);

        if (!$this->canMigrate()) {
            return $this->ensureDeploymentExists();
        }

        return $this;
    }

    protected function validateMessage(): self
    {
        if (!isset($this->jwt['body'][LtiConstants::MESSAGE_TYPE])) {
            // Unable to identify message type.
            throw new LtiException(static::ERR_INVALID_MESSAGE_TYPE);
        }

        $validator = $this->getMessageValidator($this->jwt['body']);

        if (!isset($validator)) {
            throw new LtiException(static::ERR_UNRECOGNIZED_MESSAGE_TYPE);
        }

        $validator::validate($this->jwt['body']);

        return $this;
    }

    private function getMessageValidator(array $jwtBody): ?string
    {
        $availableValidators = [
            NoticeMessageValidator::class,
        ];

        // Filter out validators that cannot validate the message
        $applicableValidators = array_filter($availableValidators, function ($validator) use ($jwtBody) {
            return $validator::canValidate($jwtBody);
        });

        // There should be 0-1 validators. This will either return the validator, or null if none apply.
        return array_shift($applicableValidators);
    }

    private function getAud(): string
    {
        if (is_array($this->jwt['body']['aud'])) {
            return $this->jwt['body']['aud'][0];
        } else {
            return $this->jwt['body']['aud'];
        }
    }

    /**
     * @throws LtiException
     */
    private function ensureDeploymentExists(): self
    {
        if (!isset($this->deployment)) {
            throw new LtiException(static::ERR_NO_DEPLOYMENT);
        }

        return $this;
    }
}
