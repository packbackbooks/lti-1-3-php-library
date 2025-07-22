<?php

namespace Packback\Lti1p3\Factories;

use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\LtiException;
use Packback\Lti1p3\LtiOidcLogin;
use Packback\Lti1p3\Messages\Notice;

class NoticeFactory extends Factory
{
    public const ERR_MISSING_NONCE = 'Missing Nonce.';
    public const ERR_INVALID_NONCE = 'Invalid Nonce.';

    public function create(array $message): Notice
    {
        [$jwt, $registration, $deployment] = $this->validate($message);

        $messageInstance = $this->createMessage($registration, $jwt);
        $messageInstance->validate();

        return $messageInstance;
    }

    public static function getTypeClaim(): string
    {
        return Claim::NOTICE;
    }

    public function getTypeName($jwt): string
    {
        return static::getClaimFrom(static::getTypeClaim(), $jwt['body'])['type'];
    }

    protected function validateState(array $message): static
    {
        /**
         * @todo Do we even need to do this?
         */
        // Check State for OIDC.
        if ($this->cookie->getCookie(LtiOidcLogin::COOKIE_PREFIX.$message['state']) !== $message['state']) {
            // Error if state doesn't match
            throw new LtiException(static::ERR_STATE_NOT_FOUND);
        }

        return $this;
    }

    protected static function getTokenKey(): string
    {
        return 'jwt';
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
}
