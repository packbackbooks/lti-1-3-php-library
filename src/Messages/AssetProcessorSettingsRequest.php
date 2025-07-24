<?php

namespace Packback\Lti1p3\Messages;

use Packback\Lti1p3\Claims\Activity;
use Packback\Lti1p3\Claims\Context;
use Packback\Lti1p3\Claims\MessageType;
use Packback\Lti1p3\Claims\Roles;
use Packback\Lti1p3\Claims\TargetLinkUri;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\Messages\Concerns\HasActivityClaim;

class AssetProcessorSettingsRequest extends LaunchMessage
{
    use HasActivityClaim;

    public static function messageType(): string
    {
        return LtiConstants::MESSAGE_TYPE_ASSETPROCESSORSETTINGS;
    }

    public static function requiredClaims(): array
    {
        return [
            MessageType::claimKey(),
            TargetLinkUri::claimKey(),
            Roles::claimKey(),
            Activity::claimKey(),
            Context::claimKey(),
        ];
    }

    public function rolesClaim(): Roles
    {
        return Roles::create($this);
    }

    /**
     * @todo build methods for the remaining required claims
     */
}
