<?php

namespace Packback\Lti1p3\Factories;

use Packback\Lti1p3\Claims\Activity;
use Packback\Lti1p3\Claims\DeepLinkSettings;
use Packback\Lti1p3\Claims\DeploymentId;
use Packback\Lti1p3\Claims\MessageType;
use Packback\Lti1p3\Claims\Notice;
use Packback\Lti1p3\Claims\PlatformNotificationService;
use Packback\Lti1p3\Concerns\Claimable;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\Messages\LtiMessage;

class ClaimFactory
{
    use Claimable;

    public static function create(string $claim, LtiMessage $message)
    {
        switch ($claim) {
            case LtiConstants::PNS_CLAIM_NOTICE:
                return new Notice(static::getClaimFrom($claim, $message->getBody()));
            case LtiConstants::AP_CLAIM_ACTIVITY:
                return new Activity(static::getClaimFrom($claim, $message->getBody()));
            case LtiConstants::PNS_CLAIM_SERVICE:
                return new PlatformNotificationService(static::getClaimFrom($claim, $message->getBody()));
            case LtiConstants::DL_DEEP_LINK_SETTINGS:
                return new DeepLinkSettings(static::getClaimFrom($claim, $message->getBody()));
            case LtiConstants::DEPLOYMENT_ID:
                return new DeploymentId(static::getClaimFrom($claim, $message->getBody()));
            case LtiConstants::MESSAGE_TYPE:
                return new MessageType(static::getClaimFrom($claim, $message->getBody()));
            default:
                throw new \InvalidArgumentException(
                    "Claim type '$claim' is not recognized or not implemented."
                );
        }
    }
}
