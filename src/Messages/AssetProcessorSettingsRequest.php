<?php

namespace Packback\Lti1p3;

use Firebase\JWT\JWT;
use Packback\Lti1p3\Interfaces\ICache;
use Packback\Lti1p3\Interfaces\ICookie;
use Packback\Lti1p3\Interfaces\IDatabase;
use Packback\Lti1p3\Interfaces\ILtiDeployment;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\MessageValidators\AssetProcessorSettingsValidator;

class AssetProcessorSettingsRequest extends LtiMessage
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
     * Returns whether or not the current launch is a EULA launch.
     */
    public function isAssetProcessorSettingsLaunch(): bool
    {
        return $this->isMessageType(LtiConstants::MESSAGE_TYPE_ASSETPROCESSORSETTINGS);
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

        return $this;
    }

    protected function validateMessage(): static
    {
        if (!isset($this->getBody()[LtiConstants::MESSAGE_TYPE])) {
            // Unable to identify message type.
            throw new LtiException(static::ERR_INVALID_MESSAGE_TYPE);
        }

        AssetProcessorSettingsValidator::validate($this->getBody());

        return $this;
    }
}
