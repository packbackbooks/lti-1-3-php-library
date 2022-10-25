<?php

namespace BNSoftware\Lti1p3;

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use BNSoftware\Lti1p3\Interfaces\ICache;
use BNSoftware\Lti1p3\Interfaces\ICookie;
use BNSoftware\Lti1p3\Interfaces\IDatabase;
use BNSoftware\Lti1p3\Interfaces\ILtiServiceConnector;
use BNSoftware\Lti1p3\MessageValidators\DeepLinkMessageValidator;
use BNSoftware\Lti1p3\MessageValidators\ResourceMessageValidator;
use BNSoftware\Lti1p3\MessageValidators\SubmissionReviewMessageValidator;
use Throwable;

class LtiMessageLaunch
{
    public const TYPE_DEEPLINK = 'LtiDeepLinkingRequest';
    public const TYPE_SUBMISSIONREVIEW = 'LtiSubmissionReviewRequest';
    public const TYPE_RESOURCELINK = 'LtiResourceLinkRequest';

    public const ERR_FETCH_PUBLIC_KEY = 'Failed to fetch public key.';
    public const ERR_NO_PUBLIC_KEY = 'Unable to find public key.';
    public const ERR_STATE_NOT_FOUND = 'Please make sure you have cookies enabled in this browser and that you are not in private or incognito mode';
    public const ERR_MISSING_ID_TOKEN = 'Missing id_token.';
    public const ERR_INVALID_ID_TOKEN = 'Invalid id_token, JWT must contain 3 parts';
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
    public const ERR_VALIDATOR_CONFLICT = 'Validator conflict.';
    public const ERR_UNRECOGNIZED_MESSAGE_TYPE = 'Unrecognized message type.';
    public const ERR_INVALID_MESSAGE = 'Message validation failed.';
    public const ERR_INVALID_ALG = 'Invalid alg was specified in the JWT header.';
    public const ERR_MISMATCHED_ALG_KEY = 'The alg specified in the JWT header is incompatible with the JWK key type.';

    private IDatabase $db;
    private ICache $cache;
    private ICookie $cookie;
    private ILtiServiceConnector $serviceConnector;
    private array $request;
    private array $jwt;
    private LtiRegistration $registration;
    private string $launchId;

    // See https://www.imsglobal.org/spec/security/v1p1#approved-jwt-signing-algorithms.
    private static array $ltiSupportedAlgs = [
        'RS256' => 'RSA',
        'RS384' => 'RSA',
        'RS512' => 'RSA',
        'ES256' => 'EC',
        'ES384' => 'EC',
        'ES512' => 'EC',
    ];

    /**
     * Constructor.
     *
     * @param IDatabase             $database         instance of the database interface used for looking up
     *                                                registrations and deployments
     * @param ?ICache               $cache            instance of the Cache interface used to loading and storing
     *                                                launches
     * @param ?ICookie              $cookie           instance of the Cookie interface used to set and read cookies
     * @param ?ILtiServiceConnector $serviceConnector instance of the LtiServiceConnector used to by LTI services to
     *                                                make API requests
     */
    public function __construct(
        IDatabase $database,
        ICache $cache = null,
        ICookie $cookie = null,
        ILtiServiceConnector $serviceConnector = null
    ) {
        $this->db = $database;

        $this->launchId = uniqid('lti1p3_launch_', true);

        $this->cache = $cache;
        $this->cookie = $cookie;
        $this->serviceConnector = $serviceConnector;
    }

    /**
     * Static function to allow for method chaining without having to assign to a variable first.
     */
    public static function new(
        IDatabase $database,
        ICache $cache = null,
        ICookie $cookie = null,
        ILtiServiceConnector $serviceConnector = null
    ): LtiMessageLaunch {
        return new LtiMessageLaunch($database, $cache, $cookie, $serviceConnector);
    }

    /**
     * Load an LtiMessageLaunch from a Cache using a launch id.
     *
     * @param string                $launchId     the launch id of the LtiMessageLaunch object that is being pulled
     *                                            from the cache
     * @param IDatabase             $database     instance of the database interface used for looking up registrations
     *                                            and deployments
     * @param ?ICache               $cache        Instance of the Cache interface used to loading and storing launches.
     *                                            If non is provided launch data will be store in $_SESSION.
     * @param ?ILtiServiceConnector $serviceConnector
     * @return LtiMessageLaunch a populated and validated LtiMessageLaunch
     * @throws LtiException will throw an LtiException if validation fails or launch cannot be found
     */
    public static function fromCache(
        string $launchId,
        IDatabase $database,
        ICache $cache = null,
        ILtiServiceConnector $serviceConnector = null
    ): LtiMessageLaunch {
        $new = new LtiMessageLaunch($database, $cache, null, $serviceConnector);
        $new->launchId = $launchId;
        $new->jwt = ['body' => $new->cache->getLaunchData($launchId)];

        return $new->validateRegistration();
    }

    /**
     * Validates all aspects of an incoming LTI message launch and caches the launch if successful.
     *
     * @param mixed $request An array of post request parameters. If not set will default to $_POST.
     *
     * @return LtiMessageLaunch will return $this if validation is successful
     * @throws LtiException will throw an LtiException if validation fails
     *
     */
    public function validate(array $request = null): LtiMessageLaunch
    {
        if ($request === null) {
            $request = $_POST;
        }
        $this->request = $request;

        return $this->validateState()
            ->validateJwtFormat()
            ->validateNonce()
            ->validateRegistration()
            ->validateJwtSignature()
            ->validateDeployment()
            ->validateMessage()
            ->cacheLaunchData();
    }

    /**
     * Returns whether or not the current launch can use the names and roles service.
     *
     * @return bool returns a boolean indicating the availability of names and roles
     */
    public function hasNrps(): bool
    {
        return !empty($this->jwt['body'][LtiConstants::NRPS_CLAIM_SERVICE]['context_memberships_url']);
    }

    /**
     * Fetches an instance of the names and roles service for the current launch.
     *
     * @return LtiNamesRolesProvisioningService an instance of the names and roles service
     *         that can be used to make calls within the scope of the current launch
     */
    public function getNrps(): LtiNamesRolesProvisioningService
    {
        return new LtiNamesRolesProvisioningService(
            $this->serviceConnector,
            $this->registration,
            $this->jwt['body'][LtiConstants::NRPS_CLAIM_SERVICE]
        );
    }

    /**
     * Returns whether or not the current launch can use the groups service.
     *
     * @return bool returns a boolean indicating the availability of groups
     */
    public function hasGs(): bool
    {
        return !empty($this->jwt['body'][LtiConstants::GS_CLAIM_SERVICE]['context_groups_url']);
    }

    /**
     * Fetches an instance of the groups service for the current launch.
     *
     * @return LtiCourseGroupsService an instance of the groups service that can be used to make calls within the scope
     *                                of the current launch
     */
    public function getGs(): LtiCourseGroupsService
    {
        return new LtiCourseGroupsService(
            $this->serviceConnector,
            $this->registration,
            $this->jwt['body'][LtiConstants::GS_CLAIM_SERVICE]
        );
    }

    /**
     * Returns whether or not the current launch can use the assignments and grades service.
     *
     * @return bool returns a boolean indicating the availability of assignments and grades
     */
    public function hasAgs(): bool
    {
        return !empty($this->jwt['body'][LtiConstants::AGS_CLAIM_ENDPOINT]);
    }

    /**
     * Fetches an instance of the assignments and grades service for the current launch.
     *
     * @return LtiAssignmentsGradesService an instance of the assignments an grades service that can be used to make
     *                                     calls within the scope of the current launch
     */
    public function getAgs(): LtiAssignmentsGradesService
    {
        return new LtiAssignmentsGradesService(
            $this->serviceConnector,
            $this->registration,
            $this->jwt['body'][LtiConstants::AGS_CLAIM_ENDPOINT]
        );
    }

    /**
     * Returns whether or not the current launch is a deep linking launch.
     *
     * @return bool returns true if the current launch is a deep linking launch
     */
    public function isDeepLinkLaunch(): bool
    {
        return $this->jwt['body'][LtiConstants::MESSAGE_TYPE] === static::TYPE_DEEPLINK;
    }

    /**
     * Fetches a deep link that can be used to construct a deep linking response.
     *
     * @return LtiDeepLink an instance of a deep link to construct a deep linking response for the current launch
     */
    public function getDeepLink(): LtiDeepLink
    {
        return new LtiDeepLink(
            $this->registration,
            $this->jwt['body'][LtiConstants::DEPLOYMENT_ID],
            $this->jwt['body'][LtiConstants::DL_DEEP_LINK_SETTINGS]
        );
    }

    /**
     * Returns whether or not the current launch is a submission review launch.
     *
     * @return bool returns true if the current launch is a submission review launch
     */
    public function isSubmissionReviewLaunch(): bool
    {
        return $this->jwt['body'][LtiConstants::MESSAGE_TYPE] === static::TYPE_SUBMISSIONREVIEW;
    }

    /**
     * Returns whether or not the current launch is a resource launch.
     *
     * @return bool returns true if the current launch is a resource launch
     */
    public function isResourceLaunch(): bool
    {
        return $this->jwt['body'][LtiConstants::MESSAGE_TYPE] === static::TYPE_RESOURCELINK;
    }

    /**
     * Fetches the decoded body of the JWT used in the current launch.
     *
     * @return array|object returns the decoded json body of the launch as an array
     */
    public function getLaunchData()
    {
        return $this->jwt['body'];
    }

    /**
     * Get the unique launch id for the current launch.
     *
     * @return string a unique identifier used to re-reference the current launch in subsequent requests
     */
    public function getLaunchId(): string
    {
        return $this->launchId;
    }

    /**
     * @param string      $issuerUrl
     * @param string|null $clientId
     * @return string
     */
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
     * @return mixed
     * @throws LtiException
     */
    private function getPublicKey()
    {
        $request = new ServiceRequest(
            ServiceRequest::METHOD_GET,
            $this->registration->getKeySetUrl(),
            ServiceRequest::TYPE_GET_KEYSET
        );

        // Download key set
        try {
            $response = $this->serviceConnector->makeRequest($request);
        } catch (Throwable $e) {
            throw new LtiException(static::ERR_NO_PUBLIC_KEY);
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
                    $keySet = JWK::parseKeySet(
                        [
                            'keys' => [$key],
                        ]
                    );
                } catch (Throwable $e) {
                    // Do nothing
                }

                if (isset($keySet[$key['kid']])) {
                    return $keySet[$key['kid']];
                }
            }
        }

        // Could not find public key with a matching kid and alg.
        throw new LtiException(static::ERR_NO_PUBLIC_KEY);
    }

    /**
     *
     * If alg is omitted from the JWK, infer it from the JWT header alg.
     * See https://datatracker.ietf.org/doc/html/rfc7517#section-4.4.
     *
     * @param array $key
     * @return string
     * @throws LtiException
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

    /**
     * @param $key
     * @return bool
     */
    private function jwtAlgMatchesJwkKty($key): bool
    {
        $jwtAlg = $this->jwt['header']['alg'];

        return isset(static::$ltiSupportedAlgs[$jwtAlg]) &&
            static::$ltiSupportedAlgs[$jwtAlg] === $key['kty'];
    }

    /**
     * @return LtiMessageLaunch
     */
    private function cacheLaunchData(): LtiMessageLaunch
    {
        $this->cache->cacheLaunchData($this->launchId, $this->jwt['body']);

        return $this;
    }

    /**
     * @return LtiMessageLaunch
     * @throws LtiException
     */
    private function validateState(): LtiMessageLaunch
    {
        // Check State for OIDC.
        if ($this->cookie->getCookie(
                LtiOidcLogin::COOKIE_PREFIX . $this->request['state']
            ) !== $this->request['state']) {
            // Error if state doesn't match
            throw new LtiException(static::ERR_STATE_NOT_FOUND);
        }

        return $this;
    }

    /**
     * @return LtiMessageLaunch
     * @throws LtiException
     */
    private function validateJwtFormat(): LtiMessageLaunch
    {
        $jwt = $this->request['id_token'] ?? null;

        if (empty($jwt)) {
            throw new LtiException(static::ERR_MISSING_ID_TOKEN);
        }

        // Get parts of JWT.
        $jwtParts = explode('.', $jwt);

        if (count($jwtParts) !== 3) {
            // Invalid number of parts in JWT.
            throw new LtiException(static::ERR_INVALID_ID_TOKEN);
        }

        // Decode JWT headers.
        $this->jwt['header'] = json_decode(JWT::urlsafeB64Decode($jwtParts[0]), true);
        // Decode JWT Body.
        $this->jwt['body'] = json_decode(JWT::urlsafeB64Decode($jwtParts[1]), true);

        return $this;
    }

    /**
     * @return LtiMessageLaunch
     * @throws LtiException
     */
    private function validateNonce(): LtiMessageLaunch
    {
        if (!isset($this->jwt['body']['nonce'])) {
            throw new LtiException(static::ERR_MISSING_NONCE);
        }
        if (!$this->cache->checkNonceIsValid($this->jwt['body']['nonce'], $this->request['state'])) {
            throw new LtiException(static::ERR_INVALID_NONCE);
        }

        return $this;
    }

    /**
     * @return LtiMessageLaunch
     * @throws LtiException
     */
    private function validateRegistration(): LtiMessageLaunch
    {
        // Find registration.
        $clientId = is_array($this->jwt['body']['aud']) ? $this->jwt['body']['aud'][0] : $this->jwt['body']['aud'];
        $issuerUrl = $this->jwt['body']['iss'];
        $this->registration = $this->db->findRegistrationByIssuer($issuerUrl, $clientId);

        if (empty($this->registration)) {
            throw new LtiException($this->getMissingRegistrationErrorMsg($issuerUrl, $clientId));
        }

        // Check client id.
        if ($clientId !== $this->registration->getClientId()) {
            // Client not registered.
            throw new LtiException(static::ERR_CLIENT_NOT_REGISTERED);
        }

        return $this;
    }

    /**
     * @return LtiMessageLaunch
     * @throws LtiException
     */
    private function validateJwtSignature(): LtiMessageLaunch
    {
        if (!isset($this->jwt['header']['kid'])) {
            throw new LtiException(static::ERR_NO_KID);
        }

        // Fetch public key.
        $publicKey = $this->getPublicKey();

        // Validate JWT signature
        try {
            JWT::decode($this->request['id_token'], $publicKey, ['RS256']);
        } catch (ExpiredException $e) {
            // Error validating signature.
            throw new LtiException(static::ERR_INVALID_SIGNATURE);
        }

        return $this;
    }

    /**
     * @return LtiMessageLaunch
     * @throws LtiException
     */
    private function validateDeployment(): LtiMessageLaunch
    {
        if (!isset($this->jwt['body'][LtiConstants::DEPLOYMENT_ID])) {
            throw new LtiException(static::ERR_MISSING_DEPLOYEMENT_ID);
        }

        // Find deployment.
        $clientId = is_array($this->jwt['body']['aud'])
            ? $this->jwt['body']['aud'][0]
            : $this->jwt['body']['aud'];
        $deployment = $this->db->findDeployment(
            $this->jwt['body']['iss'],
            $this->jwt['body'][LtiConstants::DEPLOYMENT_ID],
            $clientId
        );

        if (empty($deployment)) {
            // deployment not recognized.
            throw new LtiException(static::ERR_NO_DEPLOYMENT);
        }

        return $this;
    }

    /**
     * @return LtiMessageLaunch
     * @throws LtiException
     */
    private function validateMessage(): LtiMessageLaunch
    {
        if (empty($this->jwt['body'][LtiConstants::MESSAGE_TYPE])) {
            // Unable to identify message type.
            throw new LtiException(static::ERR_INVALID_MESSAGE_TYPE);
        }

        /**
         * @todo Fix this nonsense
         */

        // Create instances of all validators
        $validators = [
            new DeepLinkMessageValidator(),
            new ResourceMessageValidator(),
            new SubmissionReviewMessageValidator(),
        ];

        $messageValidator = false;
        foreach ($validators as $validator) {
            if ($validator->canValidate($this->jwt['body'])) {
                if ($messageValidator !== false) {
                    // Can't have more than one validator apply at a time.
                    throw new LtiException(static::ERR_VALIDATOR_CONFLICT);
                }
                $messageValidator = $validator;
            }
        }

        if ($messageValidator === false) {
            throw new LtiException(static::ERR_UNRECOGNIZED_MESSAGE_TYPE);
        }

        if (!$messageValidator->validate($this->jwt['body'])) {
            throw new LtiException(static::ERR_INVALID_MESSAGE);
        }

        return $this;
    }
}
