<?php

namespace Packback\Lti1p3\Factories;

use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\Lti1p1;
use Packback\Lti1p3\Interfaces\ICache;
use Packback\Lti1p3\Interfaces\ICookie;
use Packback\Lti1p3\Interfaces\IDatabase;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\Interfaces\IMigrationDatabase;
use Packback\Lti1p3\Lti1p1Key;
use Packback\Lti1p3\LtiDeployment;
use Packback\Lti1p3\LtiException;
use Packback\Lti1p3\LtiOidcLogin;
use Packback\Lti1p3\Messages\LaunchMessage;

class MessageFactory extends JwtPayloadFactory
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

    public static function getTypeClaim(): string
    {
        return Claim::MESSAGE_TYPE;
    }

    protected static function getTokenKey(): string
    {
        return 'id_token';
    }

    public function create(array $message): LaunchMessage
    {
        [$jwt, $registration, $deployment] = $this->validate($message);

        /**
         * @var LaunchMessage
         */
        $messageInstance = $this->createMessage($registration, $jwt);
        $this->validateClaims($messageInstance::requiredClaims(), $messageInstance->getBody());

        $this->migrate($deployment, $messageInstance)
            ->cacheLaunchData($messageInstance);

        return $messageInstance;
    }

    public function getTypeName($jwt): string
    {
        return static::getClaimFrom(static::getTypeClaim(), $jwt['body']);
    }

    /**
     * @todo handle migrations
     */
    public function migrate(?LtiDeployment $deployment, LaunchMessage $message): static
    {
        if (!$this->shouldMigrate($message)) {
            return $this->ensureDeploymentExists($deployment);
        }

        if (!$message->hasClaim(Lti1p1::class)) {
            throw new LtiException(static::ERR_OAUTH_KEY_SIGN_MISSING);
        }

        if ($message->ltiClaim1p1()->getOauthConsumerKeySign() === null) {
            throw new LtiException(static::ERR_OAUTH_KEY_SIGN_MISSING);
        }

        if (!$this->matchingLti1p1KeyExists($message)) {
            throw new LtiException(static::ERR_OAUTH_KEY_SIGN_NOT_VERIFIED);
        }

        // @phpstan-ignore method.notFound
        $deployment = $this->db->migrateFromLti1p1($message);

        return $this->ensureDeploymentExists($deployment);
    }

    public function cacheLaunchData(LaunchMessage $message): static
    {
        $this->cache->cacheLaunchData($message->getLaunchId(), $message->getBody());

        return $this;
    }

    public function canMigrate(): bool
    {
        return $this->db instanceof IMigrationDatabase;
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

    private function shouldMigrate(LaunchMessage $message): bool
    {
        return $this->canMigrate()
            // @phpstan-ignore method.notFound
            && $this->db->shouldMigrate($message);
    }

    private function matchingLti1p1KeyExists(LaunchMessage $message): bool
    {
        // @phpstan-ignore method.notFound
        $keys = $this->db->findLti1p1Keys($message);

        foreach ($keys as $key) {
            if ($this->oauthConsumerKeySignMatches($message, $key)) {
                return true;
            }
        }

        return false;
    }

    private function oauthConsumerKeySignMatches(LaunchMessage $message, Lti1p1Key $key): bool
    {
        return $message->ltiClaim1p1()->getOauthConsumerKeySign() === $this->getOauthSignature($key, $message);
    }

    private function getOauthSignature(Lti1p1Key $key, LaunchMessage $message): string
    {
        return $key->sign(
            $message->deploymentIdClaim()->getBody(),
            $message->getBody()['iss'],
            $message->getAud(),
            $message->getBody()['exp'],
            $message->getBody()['nonce']
        );
    }
}
