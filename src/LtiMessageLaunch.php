<?php

namespace Packback\Lti1p3;

use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use GuzzleHttp\Exception\TransferException;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Interfaces\ICache;
use Packback\Lti1p3\Interfaces\ICookie;
use Packback\Lti1p3\Interfaces\IDatabase;
use Packback\Lti1p3\Interfaces\ILtiDeployment;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\Interfaces\IMigrationDatabase;
use Packback\Lti1p3\MessageValidators\DeepLinkMessageValidator;
use Packback\Lti1p3\MessageValidators\ResourceMessageValidator;
use Packback\Lti1p3\MessageValidators\SubmissionReviewMessageValidator;

class LtiMessageLaunch
{
    public const TYPE_DEEPLINK = 'LtiDeepLinkingRequest';
    public const TYPE_SUBMISSIONREVIEW = 'LtiSubmissionReviewRequest';
    public const TYPE_RESOURCELINK = 'LtiResourceLinkRequest';
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

    // See https://www.imsglobal.org/spec/security/v1p1#approved-jwt-signing-algorithms.
    private static $ltiSupportedAlgs = [
        'RS256' => 'RSA',
        'RS384' => 'RSA',
        'RS512' => 'RSA',
        'ES256' => 'EC',
        'ES384' => 'EC',
        'ES512' => 'EC',
    ];
    public string $launch_id;
    private array $request;
    private array $jwt;
    private ?ILtiRegistration $registration;
    private ?ILtiDeployment $deployment;

    #[\Deprecated(message: 'use one of Packback\Lti1p3\Messages\* instead', since: '6.4')]
    public function __construct(
        private IDatabase $db,
        private ICache $cache,
        private ICookie $cookie,
        private ILtiServiceConnector $serviceConnector
    ) {
        $this->launch_id = uniqid('lti1p3_launch_', true);
    }

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

    /**
     * Load an LtiMessageLaunch from a Cache using a launch id.
     *
     * @throws LtiException Will throw an LtiException if validation fails or launch cannot be found
     */
    public static function fromCache(
        string $launch_id,
        IDatabase $db,
        ICache $cache,
        ICookie $cookie,
        ILtiServiceConnector $serviceConnector
    ): self {
        $new = new LtiMessageLaunch($db, $cache, $cookie, $serviceConnector);
        $new->launch_id = $launch_id;
        $new->jwt = ['body' => $new->cache->getLaunchData($launch_id)];

        return $new->validateRegistration();
    }

    #[\Deprecated(message: 'use Packback\Lti1p3\Factories\JwtPayloadFactory::getMissingRegistrationErrorMsg() instead', since: '6.4')]
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

    public function setRequest(array $request): self
    {
        $this->request = $request;

        return $this;
    }

    #[\Deprecated(message: 'use Packback\Lti1p3\Factories\MessageFactory::create() instead', since: '6.4')]
    public function initialize(array $request): self
    {
        return $this->setRequest($request)
            ->validate()
            ->migrate()
            ->cacheLaunchData();
    }

    /**
     * Validates all aspects of an incoming LTI message launch and caches the launch if successful.
     *
     * @throws LtiException Will throw an LtiException if validation fails
     */
    #[\Deprecated(message: 'use $messageFactory->validate() instead', since: '6.4')]
    public function validate(): self
    {
        return $this->validateState()
            ->validateJwtFormat()
            ->validateNonce()
            ->validateRegistration()
            ->validateJwtSignature()
            ->validateDeployment()
            ->validateMessage();
    }

    #[\Deprecated(message: 'use $messageFactory->migrate() instead', since: '6.4')]
    public function migrate(): self
    {
        if (!$this->shouldMigrate()) {
            return $this->ensureDeploymentExists();
        }

        if (!isset($this->jwt['body'][Claim::LTI1P1]['oauth_consumer_key_sign'])) {
            throw new LtiException(static::ERR_OAUTH_KEY_SIGN_MISSING);
        }

        if (!$this->matchingLti1p1KeyExists()) {
            throw new LtiException(static::ERR_OAUTH_KEY_SIGN_NOT_VERIFIED);
        }

        $this->deployment = $this->db->migrateFromLti1p1($this);

        return $this->ensureDeploymentExists();
    }

    #[\Deprecated(message: 'use $messageFactory->cacheLaunchData() instead', since: '6.4')]
    public function cacheLaunchData(): self
    {
        $this->cache->cacheLaunchData($this->launch_id, $this->jwt['body']);

        return $this;
    }

    /**
     * Returns whether or not the current launch can use the names and roles service.
     */
    #[\Deprecated(message: 'use $launchMessage->hasClaim(Packback\Lti1p3\Claims\NamesRoleProvisioningService::getClaimKey()) instead', since: '6.4')]
    public function hasNrps(): bool
    {
        return isset($this->jwt['body'][Claim::NRPS_NAMESROLESSERVICE]['context_memberships_url']);
    }

    /**
     * Fetches an instance of the names and roles service for the current launch.
     */
    #[\Deprecated(message: 'use $launchMessage->nrpsClaim() instead', since: '6.4')]
    public function getNrps(): LtiNamesRolesProvisioningService
    {
        return new LtiNamesRolesProvisioningService(
            $this->serviceConnector,
            $this->registration,
            $this->jwt['body'][Claim::NRPS_NAMESROLESSERVICE]
        );
    }

    /**
     * Returns whether or not the current launch can use the groups service.
     */
    #[\Deprecated(message: 'use $launchMessage->hasClaim(Packback\Lti1p3\Claims\GroupsService::getClaimKey()) instead', since: '6.4')]
    public function hasGs(): bool
    {
        return isset($this->jwt['body'][Claim::GS_GROUPSSERVICE]['context_groups_url']);
    }

    /**
     * Fetches an instance of the groups service for the current launch.
     */
    #[\Deprecated(message: 'use $launchMessage->gsClaim() instead', since: '6.4')]
    public function getGs(): LtiCourseGroupsService
    {
        return new LtiCourseGroupsService(
            $this->serviceConnector,
            $this->registration,
            $this->jwt['body'][Claim::GS_GROUPSSERVICE]
        );
    }

    /**
     * Returns whether or not the current launch can use the assignments and grades service.
     */
    #[\Deprecated(message: 'use $launchMessage->hasClaim(Packback\Lti1p3\Claims\AssignmentsGradesService::getClaimKey()) instead', since: '6.4')]
    public function hasAgs(): bool
    {
        return isset($this->jwt['body'][Claim::AGS_ENDPOINT]);
    }

    /**
     * Fetches an instance of the assignments and grades service for the current launch.
     */
    #[\Deprecated(message: 'use $launchMessage->agsClaim() instead', since: '6.4')]
    public function getAgs(): LtiAssignmentsGradesService
    {
        return new LtiAssignmentsGradesService(
            $this->serviceConnector,
            $this->registration,
            $this->jwt['body'][Claim::AGS_ENDPOINT]
        );
    }

    /**
     * Returns whether or not the current launch is a deep linking launch.
     */
    #[\Deprecated(message: 'use ($launchMessage instanceOf Packback\Lti1p3\Messages\DeepLinkingRequest) instead', since: '6.4')]
    public function isDeepLinkLaunch(): bool
    {
        return $this->jwt['body'][Claim::MESSAGE_TYPE] === static::TYPE_DEEPLINK;
    }

    /**
     * Fetches a deep link that can be used to construct a deep linking response.
     */
    #[\Deprecated(message: 'use $launchMessage->getDeepLink() instead', since: '6.4')]
    public function getDeepLink(): LtiDeepLink
    {
        return new LtiDeepLink(
            $this->registration,
            $this->jwt['body'][Claim::DEPLOYMENT_ID],
            $this->jwt['body'][Claim::DL_DEEP_LINK_SETTINGS]
        );
    }

    /**
     * Returns whether or not the current launch is a submission review launch.
     */
    #[\Deprecated(message: 'use ($launchMessage instanceOf Packback\Lti1p3\Messages\SubmissionReviewRequest) instead', since: '6.4')]
    public function isSubmissionReviewLaunch(): bool
    {
        return $this->jwt['body'][Claim::MESSAGE_TYPE] === static::TYPE_SUBMISSIONREVIEW;
    }

    /**
     * Returns whether or not the current launch is a resource launch.
     */
    #[\Deprecated(message: 'use ($launchMessage instanceOf Packback\Lti1p3\Messages\ResourceLinkRequest) instead', since: '6.4')]
    public function isResourceLaunch(): bool
    {
        return $this->jwt['body'][Claim::MESSAGE_TYPE] === static::TYPE_RESOURCELINK;
    }

    /**
     * Fetches the decoded body of the JWT used in the current launch.
     */
    #[\Deprecated(message: 'use $launchMessage->getBody() instead', since: '6.4')]
    public function getLaunchData(): array
    {
        return $this->jwt['body'];
    }

    /**
     * Get the unique launch id for the current launch.
     */
    #[\Deprecated(message: 'use $launchMessage->getLaunchId() instead', since: '6.4')]
    public function getLaunchId(): string
    {
        return $this->launch_id;
    }

    #[\Deprecated(message: 'use $messageFactory->canMigrate() instead', since: '6.4')]
    public function canMigrate(): bool
    {
        return $this->db instanceof IMigrationDatabase;
    }

    #[\Deprecated(message: 'use $messageFactory->validateState() instead', since: '6.4')]
    protected function validateState(): self
    {
        // Check State for OIDC.
        if ($this->cookie->getCookie(LtiOidcLogin::COOKIE_PREFIX.$this->request['state']) !== $this->request['state']) {
            // Error if state doesn't match
            throw new LtiException(static::ERR_STATE_NOT_FOUND);
        }

        return $this;
    }

    #[\Deprecated(message: 'use $messageFactory->validateJwtFormat() instead', since: '6.4')]
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

    #[\Deprecated(message: 'use $messageFactory->validateNonce() instead', since: '6.4')]
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

    #[\Deprecated(message: 'use $messageFactory->validateRegistration() instead', since: '6.4')]
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

    #[\Deprecated(message: 'use $messageFactory->validateJwtSignature() instead', since: '6.4')]
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

    #[\Deprecated(message: 'use $messageFactory->validateDeployment() instead', since: '6.4')]
    protected function validateDeployment(): self
    {
        if (!isset($this->jwt['body'][Claim::DEPLOYMENT_ID])) {
            throw new LtiException(static::ERR_MISSING_DEPLOYEMENT_ID);
        }

        // Find deployment.
        $client_id = $this->getAud();
        $this->deployment = $this->db->findDeployment($this->jwt['body']['iss'], $this->jwt['body'][Claim::DEPLOYMENT_ID], $client_id);

        if (!$this->canMigrate()) {
            return $this->ensureDeploymentExists();
        }

        return $this;
    }

    #[\Deprecated(message: 'use $messageFactory->validateClaims() instead', since: '6.4')]
    protected function validateMessage(): self
    {
        if (!isset($this->jwt['body'][Claim::MESSAGE_TYPE])) {
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

    private function getMessageValidator(array $jwtBody): ?string
    {
        $availableValidators = [
            DeepLinkMessageValidator::class,
            ResourceMessageValidator::class,
            SubmissionReviewMessageValidator::class,
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

    private function shouldMigrate(): bool
    {
        return $this->canMigrate()
            && $this->db->shouldMigrate($this);
    }

    private function matchingLti1p1KeyExists(): bool
    {
        $keys = $this->db->findLti1p1Keys($this);

        foreach ($keys as $key) {
            if ($this->oauthConsumerKeySignMatches($key)) {
                return true;
            }
        }

        return false;
    }

    private function oauthConsumerKeySignMatches(Lti1p1Key $key): bool
    {
        return $this->jwt['body'][Claim::LTI1P1]['oauth_consumer_key_sign'] === $this->getOauthSignature($key);
    }

    private function getOauthSignature(Lti1p1Key $key): string
    {
        return $key->sign(
            $this->jwt['body'][Claim::DEPLOYMENT_ID],
            $this->jwt['body']['iss'],
            $this->getAud(),
            $this->jwt['body']['exp'],
            $this->jwt['body']['nonce']
        );
    }
}
