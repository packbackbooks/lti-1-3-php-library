<?php

namespace Packback\Lti1p3\Messages;

use Packback\Lti1p3\Claims\EulaService;
use Packback\Lti1p3\Claims\MessageType;
use Packback\Lti1p3\Claims\Roles;
use Packback\Lti1p3\Claims\TargetLinkUri;
use Packback\Lti1p3\LtiConstants;

class EulaRequest extends LaunchMessage
{
    public static function messageType(): string
    {
        return LtiConstants::MESSAGE_TYPE_EULA;
    }

    public static function requiredClaims(): array
    {
        return [
            MessageType::claimKey(),
            TargetLinkUri::claimKey(),
            Roles::claimKey(),
            EulaService::claimKey(),
        ];
    }

    public function eulaServiceClaim(): EulaService
    {
        return EulaService::create($this);
    }
}
