<?php

namespace Packback\Lti1p3;

use Firebase\JWT\JWT;
use Packback\Lti1p3\Interfaces\ICache;
use Packback\Lti1p3\Interfaces\ICookie;
use Packback\Lti1p3\Interfaces\IDatabase;
use Packback\Lti1p3\Interfaces\ILtiDeployment;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\Interfaces\IMigrationDatabase;
use Packback\Lti1p3\MessageValidators\AssetProcessorSettingsValidator;
use Packback\Lti1p3\MessageValidators\DeepLinkMessageValidator;
use Packback\Lti1p3\MessageValidators\EulaMessageValidator;
use Packback\Lti1p3\MessageValidators\ReportReviewMessageValidator;
use Packback\Lti1p3\MessageValidators\ResourceMessageValidator;
use Packback\Lti1p3\MessageValidators\SubmissionReviewMessageValidator;
use Packback\Lti1p3\PlatformNotificationService\PlatformNotificationService;

class LtiMessageLaunch extends LtiMessage
{
    #[\Deprecated(message: 'use LtiConstants::MESSAGE_TYPE_DEEPLINK instead', since: '6.4')]
    public const TYPE_DEEPLINK = LtiConstants::MESSAGE_TYPE_DEEPLINK;

    #[\Deprecated(message: 'use LtiConstants::MESSAGE_TYPE_SUBMISSIONREVIEW instead', since: '6.4')]
    public const TYPE_SUBMISSIONREVIEW = LtiConstants::MESSAGE_TYPE_SUBMISSIONREVIEW;

    #[\Deprecated(message: 'use LtiConstants::MESSAGE_TYPE_RESOURCE instead', since: '6.4')]
    public const TYPE_RESOURCELINK = LtiConstants::MESSAGE_TYPE_RESOURCE;
    public const ERR_STATE_NOT_FOUND = 'Please make sure you have cookies and cross-site tracking enabled in the privacy and security settings of your browser.';
    public const ERR_INVALID_NONCE = 'Invalid Nonce.';
    public const ERR_NO_DEPLOYMENT = 'Unable to find deployment.';
    public const ERR_INVALID_MESSAGE_TYPE = 'Invalid message type';
    public const ERR_UNRECOGNIZED_MESSAGE_TYPE = 'Unrecognized message type.';
    public const ERR_INVALID_MESSAGE = 'Message validation failed.';
    public const ERR_INVALID_ALG = 'Invalid alg was specified in the JWT header.';
    public const ERR_OAUTH_KEY_SIGN_NOT_VERIFIED = 'Unable to upgrade from LTI 1.1 to 1.3. No OAuth Consumer Key matched this signature.';
    public const ERR_OAUTH_KEY_SIGN_MISSING = 'Unable to upgrade from LTI 1.1 to 1.3. The oauth_consumer_key_sign was not provided.';
    protected ?ILtiDeployment $deployment;
    public string $launch_id;

    public function __construct(
        protected IDatabase $db,
        protected ICache $cache,
        protected ICookie $cookie,
        protected ILtiServiceConnector $serviceConnector
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

    #[\Deprecated(message: 'use setMessage() instead', since: '6.4')]
    public function setRequest(array $request): static
    {
        return $this->setMessage($request);
    }

    public function initialize(array $request): static
    {
        return $this->setMessage($request)
            ->validate()
            ->migrate()
            ->cacheLaunchData();
    }

    /**
     * Validates all aspects of an incoming LTI message launch and caches the launch if successful.
     *
     * @throws LtiException Will throw an LtiException if validation fails
     */
    public function validate(): static
    {
        return $this->validateState()
            ->validateJwtFormat()
            ->validateNonce()
            ->validateRegistration()
            ->validateJwtSignature()
            ->validateDeployment()
            ->validateMessage();
    }

    public function migrate(): static
    {
        if (!$this->shouldMigrate()) {
            return $this->ensureDeploymentExists();
        }

        if (!isset($this->getBody()[LtiConstants::LTI1P1]['oauth_consumer_key_sign'])) {
            throw new LtiException(static::ERR_OAUTH_KEY_SIGN_MISSING);
        }

        if (!$this->matchingLti1p1KeyExists()) {
            throw new LtiException(static::ERR_OAUTH_KEY_SIGN_NOT_VERIFIED);
        }

        $this->deployment = $this->db->migrateFromLti1p1($this);

        return $this->ensureDeploymentExists();
    }

    public function cacheLaunchData(): static
    {
        $this->cache->cacheLaunchData($this->launch_id, $this->getBody());

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

    /**
     * Returns whether or not the current launch can use the names and roles service.
     */
    public function hasNrps(): bool
    {
        return isset($this->getBody()[LtiConstants::NRPS_CLAIM_SERVICE]['context_memberships_url']);
    }

    /**
     * Fetches an instance of the names and roles service for the current launch.
     */
    public function getNrps(): LtiNamesRolesProvisioningService
    {
        return new LtiNamesRolesProvisioningService(
            $this->serviceConnector,
            $this->registration,
            $this->getBody()[LtiConstants::NRPS_CLAIM_SERVICE]
        );
    }

    /**
     * Returns whether or not the current launch can use the groups service.
     */
    public function hasGs(): bool
    {
        return isset($this->getBody()[LtiConstants::GS_CLAIM_SERVICE]['context_groups_url']);
    }

    /**
     * Fetches an instance of the groups service for the current launch.
     */
    public function getGs(): LtiCourseGroupsService
    {
        return new LtiCourseGroupsService(
            $this->serviceConnector,
            $this->registration,
            $this->getBody()[LtiConstants::GS_CLAIM_SERVICE]
        );
    }

    /**
     * Returns whether or not the current launch can use the assignments and grades service.
     */
    public function hasAgs(): bool
    {
        return isset($this->getBody()[LtiConstants::AGS_CLAIM_ENDPOINT]);
    }

    /**
     * Fetches an instance of the assignments and grades service for the current launch.
     */
    public function getAgs(): LtiAssignmentsGradesService
    {
        return new LtiAssignmentsGradesService(
            $this->serviceConnector,
            $this->registration,
            $this->getBody()[LtiConstants::AGS_CLAIM_ENDPOINT]
        );
    }

    /**
     * Returns whether or not the current launch can use the assignments and grades service.
     */
    public function hasPns(): bool
    {
        return isset($this->getBody()[LtiConstants::PNS_CLAIM_SERVICE]);
    }

    /**
     * Fetches an instance of the platform notification service for the current launch.
     */
    public function getPns(): PlatformNotificationService
    {
        return new PlatformNotificationService(
            $this->getBody()[LtiConstants::PNS_CLAIM_SERVICE]
        );
    }

    /**
     * Returns whether or not the current launch is a deep linking launch.
     */
    public function isDeepLinkLaunch(): bool
    {
        return $this->getBody()[LtiConstants::MESSAGE_TYPE] === LtiConstants::MESSAGE_TYPE_DEEPLINK;
    }

    /**
     * Fetches a deep link that can be used to construct a deep linking response.
     */
    public function getDeepLink(): LtiDeepLink
    {
        return new LtiDeepLink(
            $this->registration,
            $this->getBody()[LtiConstants::DEPLOYMENT_ID],
            $this->getBody()[LtiConstants::DL_DEEP_LINK_SETTINGS]
        );
    }

    /**
     * Returns whether or not the current launch is a submission review launch.
     */
    public function isSubmissionReviewLaunch(): bool
    {
        return $this->getBody()[LtiConstants::MESSAGE_TYPE] === LtiConstants::MESSAGE_TYPE_SUBMISSIONREVIEW;
    }

    /**
     * Returns whether or not the current launch is a resource launch.
     */
    public function isResourceLaunch(): bool
    {
        return $this->getBody()[LtiConstants::MESSAGE_TYPE] === LtiConstants::MESSAGE_TYPE_RESOURCE;
    }

    /**
     * Returns whether or not the current launch is a EULA launch.
     */
    public function isEulaLaunch(): bool
    {
        return $this->getBody()[LtiConstants::MESSAGE_TYPE] === LtiConstants::MESSAGE_TYPE_EULA;
    }

    /**
     * Returns whether or not the current launch is a EULA launch.
     */
    public function isReportReviewLaunch(): bool
    {
        return $this->getBody()[LtiConstants::MESSAGE_TYPE] === LtiConstants::MESSAGE_TYPE_REPORTREVIEW;
    }

    /**
     * Fetches the decoded body of the JWT used in the current launch.
     */
    public function getLaunchData(): array
    {
        return $this->getBody();
    }

    /**
     * Get the unique launch id for the current launch.
     */
    public function getLaunchId(): string
    {
        return $this->launch_id;
    }

    protected function hasJwtToken(): bool
    {
        return isset($this->message['id_token']);
    }

    protected function getJwtToken(): string
    {
        return $this->message['id_token'];
    }

    protected function validateState(): static
    {
        // Check State for OIDC.
        if ($this->cookie->getCookie(LtiOidcLogin::COOKIE_PREFIX.$this->message['state']) !== $this->message['state']) {
            // Error if state doesn't match
            throw new LtiException(static::ERR_STATE_NOT_FOUND);
        }

        return $this;
    }

    protected function validateNonce(): static
    {
        parent::validateNonce();

        if (!$this->cache->checkNonceIsValid($this->getBody()['nonce'], $this->message['state'])) {
            throw new LtiException(static::ERR_INVALID_NONCE);
        }

        return $this;
    }

    protected function validateDeployment(): static
    {
        parent::validateDeployment();

        // Find deployment.
        $client_id = $this->getAud();
        $this->deployment = $this->db->findDeployment($this->getBody()['iss'], $this->getBody()[LtiConstants::DEPLOYMENT_ID], $client_id);

        if (!$this->canMigrate()) {
            return $this->ensureDeploymentExists();
        }

        return $this;
    }

    protected function validateMessage(): static
    {
        if (!isset($this->getBody()[LtiConstants::MESSAGE_TYPE])) {
            // Unable to identify message type.
            throw new LtiException(static::ERR_INVALID_MESSAGE_TYPE);
        }

        $validator = $this->getMessageValidator($this->getBody());

        if (!isset($validator)) {
            throw new LtiException(static::ERR_UNRECOGNIZED_MESSAGE_TYPE);
        }

        $validator::validate($this->getBody());

        return $this;
    }

    private function getMessageValidator(array $jwtBody): ?string
    {
        $availableValidators = [
            DeepLinkMessageValidator::class,
            ReportReviewMessageValidator::class,
            ResourceMessageValidator::class,
            SubmissionReviewMessageValidator::class,
            EulaMessageValidator::class,
            AssetProcessorSettingsValidator::class,
        ];

        // Filter out validators that cannot validate the message
        $applicableValidators = array_filter($availableValidators, function ($validator) use ($jwtBody) {
            return $validator::canValidate($jwtBody);
        });

        // There should be 0-1 validators. This will either return the validator, or null if none apply.
        return array_shift($applicableValidators);
    }

    /**
     * @throws LtiException
     */
    private function ensureDeploymentExists(): static
    {
        if (!isset($this->deployment)) {
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
        return $this->getBody()[LtiConstants::LTI1P1]['oauth_consumer_key_sign'] === $this->getOauthSignature($key);
    }

    private function getOauthSignature(Lti1p1Key $key): string
    {
        return $key->sign(
            $this->getBody()[LtiConstants::DEPLOYMENT_ID],
            $this->getBody()['iss'],
            $this->getAud(),
            $this->getBody()['exp'],
            $this->getBody()['nonce']
        );
    }
}
