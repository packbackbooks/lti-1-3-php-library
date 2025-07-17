<?php

namespace Packback\Lti1p3\Messages;

use Firebase\JWT\JWT;
use Packback\Lti1p3\Interfaces\ICache;
use Packback\Lti1p3\Interfaces\ICookie;
use Packback\Lti1p3\Interfaces\IDatabase;
use Packback\Lti1p3\Interfaces\ILtiDeployment;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\Interfaces\IMigrationDatabase;
use Packback\Lti1p3\Lti1p1Key;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\LtiException;
use Packback\Lti1p3\LtiOidcLogin;
use Packback\Lti1p3\LtiRegistration;

abstract class LaunchMessage extends LtiMessage
{
    #[\Deprecated(message: 'use LtiConstants::MESSAGE_TYPE_DEEPLINK instead', since: '6.4')]
    public const TYPE_DEEPLINK = LtiConstants::MESSAGE_TYPE_DEEPLINK;

    #[\Deprecated(message: 'use LtiConstants::MESSAGE_TYPE_SUBMISSIONREVIEW instead', since: '6.4')]
    public const TYPE_SUBMISSIONREVIEW = LtiConstants::MESSAGE_TYPE_SUBMISSIONREVIEW;

    #[\Deprecated(message: 'use LtiConstants::MESSAGE_TYPE_RESOURCE instead', since: '6.4')]
    public const TYPE_RESOURCELINK = LtiConstants::MESSAGE_TYPE_RESOURCE;
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

    abstract public static function messageType(): string;

    public function __construct(
        protected IDatabase $db,
        protected ICache $cache,
        protected ICookie $cookie,
        protected ILtiServiceConnector $serviceConnector
    ) {
        $this->launch_id = uniqid('lti1p3_launch_', true);
    }

    public function initialize(array $request): static
    {
        return $this->setMessage($request)
            ->validate()
            ->migrate()
            ->cacheLaunchData();
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

    /**
     * Validates all aspects of an incoming LTI message launch and caches the launch if successful.
     *
     * @throws LtiException Will throw an LtiException if validation fails
     */
    public function validate(): static
    {
        return $this->validateState()
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

    /**
     * @throws LtiException
     */
    protected function ensureDeploymentExists(): static
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
