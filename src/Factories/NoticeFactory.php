<?php

namespace Packback\Lti1p3\Factories;

use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\DeploymentId;
use Packback\Lti1p3\Claims\Version;
use Packback\Lti1p3\Helpers\Claims;
use Packback\Lti1p3\Messages\Notice;

class NoticeFactory extends JwtPayloadFactory
{
    public const ERR_MISSING_NONCE = 'Missing Nonce.';
    public const ERR_INVALID_NONCE = 'Invalid Nonce.';

    public static function getTypeClaim(): string
    {
        return Claim::NOTICE;
    }

    protected static function getTokenKey(): string
    {
        return 'jwt';
    }

    public function create(array $message): Notice
    {
        [$jwt, $registration, $deployment] = $this->validate($message);

        /**
         * @var Notice
         */
        $messageInstance = $this->createMessage($registration, $jwt);
        $this->validateClaims($messageInstance::requiredClaims(), $messageInstance->getBody());

        return $messageInstance;
    }

    public function getTypeName($jwt): string
    {
        return Claims::getClaimFrom(static::getTypeClaim(), $jwt['body'])['type'];
    }

    protected function validateState(array $message): static
    {
        // Notices have no state.
        return $this;
    }

    protected function validateNonce(array $jwt, array $message): static
    {
        // Notices seem to have a nonce, but no obvious way to validate them.
        return $this;
    }

    /**
     * @return array<string>
     */
    protected function requiredClaims(): array
    {
        return [
            Version::claimKey(),
            DeploymentId::claimKey(),
            static::getTypeClaim(),
        ];
    }
}
