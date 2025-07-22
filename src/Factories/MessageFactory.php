<?php

namespace Packback\Lti1p3\Factories;

use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Interfaces\ICache;
use Packback\Lti1p3\Interfaces\ICookie;
use Packback\Lti1p3\Interfaces\IDatabase;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\Interfaces\IMigrationDatabase;
use Packback\Lti1p3\Lti1p1Key;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\LtiDeployment;
use Packback\Lti1p3\LtiException;
use Packback\Lti1p3\LtiOidcLogin;
use Packback\Lti1p3\Messages\LaunchMessage;

class MessageFactory extends Factory
{
    public const ERR_MISSING_NONCE = 'Missing Nonce.';
    public const ERR_INVALID_NONCE = 'Invalid Nonce.';
    public const ERR_OAUTH_KEY_SIGN_NOT_VERIFIED = 'Unable to upgrade from LTI 1.1 to 1.3. No OAuth Consumer Key matched this signature.';
    public const ERR_OAUTH_KEY_SIGN_MISSING = 'Unable to upgrade from LTI 1.1 to 1.3. The oauth_consumer_key_sign was not provided.';

    public function __construct(
        IDatabase $db,
        ILtiServiceConnector $serviceConnector,
        protected ICache $cache,
        protected ICookie $cookie,
    ) {
        parent::__construct($db, $serviceConnector);
    }

    public function create(array $message): LaunchMessage
    {
        [$jwt, $registration, $deployment] = $this->validate($message);

        /**
         * @var LaunchMessage
         */
        $messageInstance = $this->createMessage($registration, $jwt);
        $messageInstance->validate();

        $this->migrate($deployment, $jwt)
            ->cacheLaunchData($messageInstance, $jwt);

        return $messageInstance;
    }

    public static function getTypeClaim(): string
    {
        return LtiConstants::MESSAGE_TYPE;
    }

    public function getTypeName($jwt): string
    {
        return static::getClaimFrom(static::getTypeClaim(), $jwt['body']);
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

    protected static function getTokenKey(): string
    {
        return 'id_token';
    }

    protected function validateNonce(array $jwt, array $message): static
    {
        if (!isset($jwt['body']['nonce'])) {
            throw new LtiException(static::ERR_MISSING_NONCE);
        }

        if (!$this->cache->checkNonceIsValid($jwt['body']['nonce'], $message['state'])) {
            throw new LtiException(static::ERR_INVALID_NONCE);
        }

        return $this;
    }

    protected function validateDeployment(array $jwt): ?LtiDeployment
    {
        $deployment = parent::validateDeployment($jwt);

        /**
         * @todo if is launch
         */
        if (!$this->canMigrate()) {
            $this->ensureDeploymentExists($deployment);
        }

        return $deployment;
    }

    /**
     * @todo handle migrations
     */
    public function migrate(?LtiDeployment $deployment, array $jwt): static
    {
        if (!$this->shouldMigrate()) {
            return $this->ensureDeploymentExists($deployment);
        }

        if (!isset($jwt['body'][Claim::LTI1P1]['oauth_consumer_key_sign'])) {
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

    public function cacheLaunchData(LaunchMessage $message, array $jwt): static
    {
        $this->cache->cacheLaunchData($message->getLaunchId(), $jwt['body']);

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
        return $jwt['body'][Claim::LTI1P1]['oauth_consumer_key_sign'] === $this->getOauthSignature($key, $jwt);
    }

    private function getOauthSignature(Lti1p1Key $key, array $jwt): string
    {
        return $key->sign(
            $jwt['body'][Claim::DEPLOYMENT_ID],
            $jwt['body']['iss'],
            $this->getAud($jwt),
            $jwt['body']['exp'],
            $jwt['body']['nonce']
        );
    }
}
