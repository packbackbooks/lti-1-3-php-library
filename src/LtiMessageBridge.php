<?php

namespace Packback\Lti1p3;

use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use GuzzleHttp\Exception\TransferException;
use Packback\Lti1p3\Concerns\Claimable;
use Packback\Lti1p3\Interfaces\ICache;
use Packback\Lti1p3\Interfaces\ICookie;
use Packback\Lti1p3\Interfaces\IDatabase;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\Messages\AssetProcessorSettingRequest;
use Packback\Lti1p3\Messages\DeepLinkingRequest;
use Packback\Lti1p3\Messages\EulaRequest;
use Packback\Lti1p3\Messages\Notice;
use Packback\Lti1p3\Messages\ReportReviewRequest;
use Packback\Lti1p3\Messages\ResourceLinkRequest;

class LtiMessageBridge
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
    public const ERR_OAUTH_KEY_SIGN_NOT_VERIFIED = 'Unable to upgrade from LTI 1.1 to 1.3. No OAuth Consumer Key matched this signature.';
    public const ERR_OAUTH_KEY_SIGN_MISSING = 'Unable to upgrade from LTI 1.1 to 1.3. The oauth_consumer_key_sign was not provided.';
    protected ?ILtiDeployment $deployment;
    public string $launch_id;
    protected array $message;
    protected array $jwt;
    protected ?ILtiRegistration $registration;

    // See https://www.imsglobal.org/spec/security/v1p1#approved-jwt-signing-algorithms.
    protected static $ltiSupportedAlgs = [
        'RS256' => 'RSA',
        'RS384' => 'RSA',
        'RS512' => 'RSA',
        'ES256' => 'EC',
        'ES384' => 'EC',
        'ES512' => 'EC',
    ];
    protected static $claimTokenKeyMap = [
        LtiConstants::MESSAGE_TYPE => 'id_token',
        LtiConstants::PNS_CLAIM_NOTICE => 'jwt',
    ];

    public function __construct(
        protected IDatabase $db,
        protected ICache $cache,
        protected ICookie $cookie,
        protected ILtiServiceConnector $serviceConnector
    ) {}

    public function initialize(array $message, string $typeClaim): static
    {
        $this->setMessage($message);
        [$jwt, $registration, $deployment] = $this->validate($message, $typeClaim);

        $messageInstance = $this->createMessage($jwt, $typeClaim);
        $messageInstance->validate();

        /**
         * @todo There should probably be a separate class for messages and notices
         */
        if ($typeClaim === LtiConstants::MESSAGE_TYPE) {
            $this->migrate($deployment, $jwt)
                ->cacheLaunchData(uniqid('lti1p3_launch_', true), $jwt);
        }

        return $this;
    }

    /**
     * Validates all aspects of an incoming LTI message launch and caches the launch if successful.
     *
     * @throws LtiException Will throw an LtiException if validation fails
     */
    public function validate(array $message, string $typeClaim): array
    {
        if (isset($message['state'])) {
            $this->validateState($message);
        }

        $tokenKey = static::getTokenKey($typeClaim);

        $jwt = $this->validateJwtFormat($message, $tokenKey);
        $this->validateNonce($jwt, $message);
        $registration = $this->validateRegistration($jwt);
        $this->validateJwtSignature($registration, $jwt, $message[$tokenKey]);
        $deployment = $this->validateDeployment($jwt);
        $this->validateUniversalClaims($jwt);

        return [$jwt, $registration, $deployment];
    }

    public function createMessage(array $jwt, string $typeClaim): LtiMessage
    {
        $class = $this->getMessageClass($jwt, $typeClaim);

        return new $class($jwt['body']);
    }

    public function getMessageClass(array $jwt, string $typeClaim): string
    {
        if ($typeClaim === LtiConstants::MESSAGE_TYPE) {
            $type = $this->getClaim($jwt, $typeClaim)->getBody();
        } elseif ($typeClaim === LtiConstants::PNS_CLAIM_NOTICE) {
            $type = $this->getClaim($jwt, $typeClaim)->getBody()['type'];
        }

        /**
         * @todo There should probably be a separate class for messages and notices
         */
        $typeClaimMap = [
            // Messages
            LtiConstants::MESSAGE_TYPE_DEEPLINK => DeepLinkingRequest::class,
            LtiConstants::MESSAGE_TYPE_RESOURCE => ResourceLinkRequest::class,
            /**
             * @todo what is this?
             */
            // LtiConstants::MESSAGE_TYPE_SUBMISSIONREVIEW => SubmissionReviewRequest::class,
            LtiConstants::MESSAGE_TYPE_EULA => EulaRequest::class,
            LtiConstants::MESSAGE_TYPE_REPORTREVIEW => ReportReviewRequest::class,
            LtiConstants::MESSAGE_TYPE_ASSETPROCESSORSETTINGS => AssetProcessorSettingRequest::class,
            // Notices
            LtiConstants::NOTICE_TYPE_HELLOWORLD => Notice::class,
            LtiConstants::NOTICE_TYPE_CONTEXTCOPY => Notice::class,
            LtiConstants::NOTICE_TYPE_ASSETPROCESSORSUBMISSION => Notice::class,
        ];

        return $typeClaimMap[$type];
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

    public function setMessage(array $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getServiceConnector(): ILtiServiceConnector
    {
        return $this->serviceConnector;
    }

    public function getRegistration(): LtiRegistration
    {
        return $this->registration;
    }

    protected function validateState(array $message): static
    {
        // Check State for OIDC.
        if ($this->cookie->getCookie(LtiOidcLogin::COOKIE_PREFIX.$message['state']) !== $message['state']) {
            // Error if state doesn't match
            throw new LtiException(static::ERR_STATE_NOT_FOUND);
        }

        return $this;
    }

    protected function validateMessage(array $message, string $typeClaim): static
    {
        $validator = $this->messageValidator($this->getBody());

        if (!isset($validator)) {
            throw new LtiException(static::ERR_UNRECOGNIZED_MESSAGE_TYPE);
        }

        $validator::validate($this->getBody());

        return $this;
    }

    protected function validateJwtFormat(array $message, string $tokenKey): array
    {
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

    protected static function getTokenKey(string $claim): string
    {
        return static::$claimTokenKeyMap[$claim];
    }

    protected function validateNonce(array $jwt, array $message): static
    {
        if (!isset($jwt['body']['nonce'])) {
            throw new LtiException(static::ERR_MISSING_NONCE);
        }

        /**
         * @todo, how do we do this for async notifications?
         */
        if (isset($this->cache) && !$this->cache->checkNonceIsValid($jwt['body']['nonce'], $message['state'])) {
            throw new LtiException(static::ERR_INVALID_NONCE);
        }

        return $this;
    }

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

    protected function validateJwtSignature(ILtiRegistration $registration, array $jwt, string $encodedJwt): static
    {
        if (!isset($jwt['header']['kid'])) {
            throw new LtiException(static::ERR_NO_KID);
        }

        // Fetch public key.
        $public_key = $this->getPublicKey($registration, $jwt);
        $headers = new \stdClass;

        // Validate JWT signature
        try {
            JWT::decode($encodedJwt, $public_key, $headers);
        } catch (ExpiredException $e) {
            // Error validating signature.
            throw new LtiException(static::ERR_INVALID_SIGNATURE, previous: $e);
        }

        return $this;
    }

    protected function validateDeployment(array $jwt): ?LtiDeployment
    {
        if (!isset($jwt['body'][LtiConstants::DEPLOYMENT_ID])) {
            throw new LtiException(static::ERR_MISSING_DEPLOYEMENT_ID);
        }

        // Find deployment.
        $client_id = $this->getAud($jwt);
        $deployment = $this->db->findDeployment($jwt['body']['iss'], $jwt['body'][LtiConstants::DEPLOYMENT_ID], $client_id);

        /**
         * @todo if is launch
         */
        if (!$this->canMigrate()) {
            $this->ensureDeploymentExists($deployment);
        }

        return $deployment;
    }

    protected function universallyRequiredClaims(): array
    {
        return [
            LtiConstants::VERSION,
            LtiConstants::DEPLOYMENT_ID,
            LtiConstants::ROLES,
        ];
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
     * @todo handle migrations
     */
    public function migrate(?LtiDeployment $deployment, array $jwt): static
    {
        if (!$this->shouldMigrate()) {
            return $this->ensureDeploymentExists($deployment);
        }

        if (!isset($jwt['body'][LtiConstants::LTI1P1]['oauth_consumer_key_sign'])) {
            throw new LtiException(static::ERR_OAUTH_KEY_SIGN_MISSING);
        }

        if (!$this->matchingLti1p1KeyExists($jwt)) {
            throw new LtiException(static::ERR_OAUTH_KEY_SIGN_NOT_VERIFIED);
        }

        /**
         * @todo figure out what to do about this
         */
        $deployment = $this->db->migrateFromLti1p1($this);

        return $this->ensureDeploymentExists($deployment);
    }

    public function cacheLaunchData(string $launchId, array $jwt): static
    {
        $this->cache->cacheLaunchData($this->launch_id, $jwt['body']);

        return $this;
    }

    /**
     * Get the unique launch id for the current launch.
     */
    public function getLaunchId(): string
    {
        return $this->launch_id;
    }

    protected function hasJwtToken(array $message): bool
    {
        return isset($message['id_token']);
    }

    protected function getJwtToken(array $message): string
    {
        return $this->message['id_token'];
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

    public function canMigrate(): bool
    {
        return $this->db instanceof IMigrationDatabase;
    }

    private function shouldMigrate(): bool
    {
        /**
         * @todo figure out what to do here
         */
        return $this->canMigrate()
            && $this->db->shouldMigrate($this);
    }

    private function matchingLti1p1KeyExists(array $jwt): bool
    {
        /**
         * @todo figure out what to do here
         */
        $keys = $this->db->findLti1p1Keys($this);

        foreach ($keys as $key) {
            if ($this->oauthConsumerKeySignMatches($jwt, $key)) {
                return true;
            }
        }

        return false;
    }

    private function oauthConsumerKeySignMatches(array $jwt, Lti1p1Key $key): bool
    {
        return $jwt['body'][LtiConstants::LTI1P1]['oauth_consumer_key_sign'] === $this->getOauthSignature($key, $jwt);
    }

    private function getOauthSignature(Lti1p1Key $key, array $jwt): string
    {
        return $key->sign(
            $jwt['body'][LtiConstants::DEPLOYMENT_ID],
            $jwt['body']['iss'],
            $this->getAud($jwt),
            $jwt['body']['exp'],
            $jwt['body']['nonce']
        );
    }
}
