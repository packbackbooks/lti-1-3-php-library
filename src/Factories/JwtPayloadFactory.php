<?php

namespace Packback\Lti1p3\Factories;

use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use GuzzleHttp\Exception\TransferException;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Concerns\Claimable;
use Packback\Lti1p3\Interfaces\IDatabase;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\LtiDeployment;
use Packback\Lti1p3\LtiException;
use Packback\Lti1p3\Messages\AssetProcessorSettingsRequest;
use Packback\Lti1p3\Messages\DeepLinkingRequest;
use Packback\Lti1p3\Messages\EulaRequest;
use Packback\Lti1p3\Messages\LtiMessage;
use Packback\Lti1p3\Messages\Notice;
use Packback\Lti1p3\Messages\ReportReviewRequest;
use Packback\Lti1p3\Messages\ResourceLinkRequest;
use Packback\Lti1p3\ServiceRequest;

abstract class JwtPayloadFactory
{
    use Claimable;
    public const ERR_FETCH_PUBLIC_KEY = 'Failed to fetch public key.';
    public const ERR_NO_PUBLIC_KEY = 'Unable to find public key.';
    public const ERR_NO_MATCHING_PUBLIC_KEY = 'Unable to find a public key which matches your JWT.';
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
    public const ERR_MISMATCHED_ALG_KEY = 'The alg specified in the JWT header is incompatible with the JWK key type.';
    public const ERR_STATE_NOT_FOUND = 'Please make sure you have cookies and cross-site tracking enabled in the privacy and security settings of your browser.';
    public const ERR_NO_DEPLOYMENT = 'Unable to find deployment.';
    public const ERR_INVALID_MESSAGE_TYPE = 'Invalid message type';
    public const ERR_UNRECOGNIZED_MESSAGE_TYPE = 'Unrecognized message type.';
    public const ERR_INVALID_MESSAGE = 'Message validation failed.';
    public const ERR_INVALID_ALG = 'Invalid alg was specified in the JWT header.';

    // See https://www.imsglobal.org/spec/security/v1p1#approved-jwt-signing-algorithms.
    protected static $ltiSupportedAlgs = [
        'RS256' => 'RSA',
        'RS384' => 'RSA',
        'RS512' => 'RSA',
        'ES256' => 'EC',
        'ES384' => 'EC',
        'ES512' => 'EC',
    ];

    public function __construct(
        protected IDatabase $db,
        protected ILtiServiceConnector $serviceConnector
    ) {}

    abstract public function create(array $message): LtiMessage;
    // {
    //     $this->setMessage($message);
    //     [$jwt, $registration, $deployment] = $this->validate($message, $typeClaim);

    //     $messageInstance = $this->createMessage($jwt, $typeClaim);
    //     $messageInstance->validate();

    //     return $this;
    // }

    /**
     * Validates all aspects of an incoming LTI message launch and caches the launch if successful.
     *
     * @throws LtiException Will throw an LtiException if validation fails
     */
    public function validate(array $message): array
    {
        if (isset($message['state'])) {
            $this->validateState($message);
        }

        $jwt = $this->validateJwtFormat($message);
        $this->validateNonce($jwt, $message);
        $registration = $this->validateRegistration($jwt);
        $this->validateJwtSignature($registration, $jwt, $message);
        $this->validateRequiredClaims($jwt);
        $deployment = $this->validateDeployment($jwt);

        return [$jwt, $registration, $deployment];
    }

    public function createMessage(ILtiRegistration $registration, array $jwt): LtiMessage
    {
        switch ($this->getTypeName($jwt)) {
            case LtiConstants::MESSAGE_TYPE_DEEPLINK:
                return new DeepLinkingRequest($this->serviceConnector, $registration, $jwt['body']);
            case LtiConstants::MESSAGE_TYPE_RESOURCE:
                return new ResourceLinkRequest($this->serviceConnector, $registration, $jwt['body']);
            case LtiConstants::MESSAGE_TYPE_EULA:
                return new EulaRequest($this->serviceConnector, $registration, $jwt['body']);
            case LtiConstants::MESSAGE_TYPE_REPORTREVIEW:
                return new ReportReviewRequest($this->serviceConnector, $registration, $jwt['body']);
            case LtiConstants::MESSAGE_TYPE_ASSETPROCESSORSETTINGS:
                return new AssetProcessorSettingsRequest($this->serviceConnector, $registration, $jwt['body']);
            case LtiConstants::NOTICE_TYPE_HELLOWORLD:
            case LtiConstants::NOTICE_TYPE_CONTEXTCOPY:
            case LtiConstants::NOTICE_TYPE_ASSETPROCESSORSUBMISSION:
                return new Notice($this->serviceConnector, $registration, $jwt['body']);
            default:
                throw new LtiException(static::ERR_INVALID_MESSAGE_TYPE);
        }
    }

    abstract public static function getTypeClaim(): string;

    abstract public function getTypeName($jwt): string;

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

    abstract protected function validateState(array $message): static;
    // {
    //     // Check State for OIDC.
    //     if ($this->cookie->getCookie(LtiOidcLogin::COOKIE_PREFIX.$message['state']) !== $message['state']) {
    //         // Error if state doesn't match
    //         throw new LtiException(static::ERR_STATE_NOT_FOUND);
    //     }

    //     return $this;
    // }

    protected function validateJwtFormat(array $message): array
    {
        $tokenKey = static::getTokenKey();
        if (!isset($message[$tokenKey])) {
            throw new LtiException(static::ERR_MISSING_ID_TOKEN);
        }

        // Get parts of JWT.
        $jwt_parts = explode('.', $message[$tokenKey]);

        if (count($jwt_parts) !== 3) {
            // Invalid number of parts in JWT.
            throw new LtiException(static::ERR_INVALID_ID_TOKEN);
        }

        // Decode JWT headers.
        $jwt['header'] = json_decode(JWT::urlsafeB64Decode($jwt_parts[0]), true);
        // Decode JWT Body.
        $jwt['body'] = json_decode(JWT::urlsafeB64Decode($jwt_parts[1]), true);

        return $jwt;
    }

    abstract protected static function getTokenKey(): string;
    // {
    //     return static::$claimTokenKeyMap[$claim];
    // }

    abstract protected function validateNonce(array $jwt, array $message): static;
    // {
    //     if (!isset($jwt['body']['nonce'])) {
    //         throw new LtiException(static::ERR_MISSING_NONCE);
    //     }

    //     /**
    //      * @todo, how do we do this for async notifications?
    //      */
    //     if (isset($this->cache) && !$this->cache->checkNonceIsValid($jwt['body']['nonce'], $message['state'])) {
    //         throw new LtiException(static::ERR_INVALID_NONCE);
    //     }

    //     return $this;
    // }

    protected function validateRegistration(array $jwt): ILtiRegistration
    {
        // Find registration.
        $clientId = $this->getAud($jwt);
        $issuerUrl = $jwt['body']['iss'];
        $registration = $this->db->findRegistrationByIssuer($issuerUrl, $clientId);

        if (!isset($registration)) {
            throw new LtiException($this->getMissingRegistrationErrorMsg($issuerUrl, $clientId));
        }

        // Check client id.
        if ($clientId !== $registration->getClientId()) {
            // Client not registered.
            throw new LtiException(static::ERR_CLIENT_NOT_REGISTERED);
        }

        return $registration;
    }

    protected function validateJwtSignature(ILtiRegistration $registration, array $jwt, array $message): static
    {
        if (!isset($jwt['header']['kid'])) {
            throw new LtiException(static::ERR_NO_KID);
        }

        // Fetch public key.
        $public_key = $this->getPublicKey($registration, $jwt);
        $headers = new \stdClass;

        // Validate JWT signature
        try {
            JWT::decode($message[static::getTokenKey()], $public_key, $headers);
        } catch (ExpiredException $e) {
            // Error validating signature.
            throw new LtiException(static::ERR_INVALID_SIGNATURE, previous: $e);
        }

        return $this;
    }

    protected function validateRequiredClaims(array $jwt): static
    {
        $requiredClaims = [
            Claim::VERSION,
            Claim::DEPLOYMENT_ID,
            Claim::ROLES,
            static::getTypeClaim(),
        ];
        foreach ($requiredClaims as $claim) {
            if (!static::hasClaimInBody($claim, $jwt['body'])) {
                // Unable to identify message type.
                throw new LtiException('Missing required claim: '.$claim);
            }
        }

        return $this;
    }

    protected function validateDeployment(array $jwt): ?LtiDeployment
    {
        // Find deployment.
        $client_id = $this->getAud($jwt);
        /**
         * @var ?LtiDeployment
         */
        $deployment = $this->db->findDeployment($jwt['body']['iss'], $jwt['body'][Claim::DEPLOYMENT_ID], $client_id);

        return $deployment;
    }

    protected function getAud(array $jwt): string
    {
        if (is_array($jwt['body']['aud'])) {
            return $jwt['body']['aud'][0];
        } else {
            return $jwt['body']['aud'];
        }
    }

    /**
     * @throws LtiException
     */
    private function getPublicKey(ILtiRegistration $registration, array $jwt): Key
    {
        $request = new ServiceRequest(
            ServiceRequest::METHOD_GET,
            $registration->getKeySetUrl(),
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
            if ($key['kid'] == $jwt['header']['kid']) {
                $key['alg'] = $this->getKeyAlgorithm($key, $jwt);

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
    private function getKeyAlgorithm(array $key, array $jwt): string
    {
        if (isset($key['alg'])) {
            return $key['alg'];
        }

        // The header alg must match the key type (family) specified in the JWK's kty.
        if ($this->jwtAlgMatchesJwkKty($jwt, $key)) {
            return $jwt['header']['alg'];
        }

        throw new LtiException(static::ERR_MISMATCHED_ALG_KEY);
    }

    private function jwtAlgMatchesJwkKty(array $jwt, array $key): bool
    {
        $jwtAlg = $jwt['header']['alg'];

        return isset(static::$ltiSupportedAlgs[$jwtAlg]) &&
            static::$ltiSupportedAlgs[$jwtAlg] === $key['kty'];
    }

    /**
     * @throws LtiException
     */
    protected function ensureDeploymentExists(?LtiDeployment $deployment = null): static
    {
        if (!isset($deployment)) {
            throw new LtiException(static::ERR_NO_DEPLOYMENT);
        }

        return $this;
    }
}
