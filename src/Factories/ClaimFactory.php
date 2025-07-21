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
                // case LtiConstants::VERSION:
                // case LtiConstants::ROLES:
                // case LtiConstants::FOR_USER:
                // case LtiConstants::TARGET_LINK_URI:
                // case LtiConstants::RESOURCE_LINK:
                // case LtiConstants::CONTEXT:
                // case LtiConstants::CUSTOM:
                // case LtiConstants::LAUNCH_PRESENTATION:
                // case LtiConstants::LIS:
                // case LtiConstants::LTI1P1:
                // case LtiConstants::ROLE_SCOPE_MENTOR:
                // case LtiConstants::TOOL_PLATFORM:
                // case LtiConstants::DL_CONTENT_ITEMS:
                // case LtiConstants::DL_DATA:
                // case LtiConstants::NRPS_CLAIM_SERVICE:
                // case LtiConstants::AGS_CLAIM_ENDPOINT:
                // case LtiConstants::GS_CLAIM_SERVICE:
                // case LtiConstants::AP_CLAIM_SERVICE:
                // case LtiConstants::AP_CLAIM_REPORT:
                // case LtiConstants::AP_CLAIM_SUBMISSION:
                // case LtiConstants::AP_CLAIM_REPORT_TYPE:
                // case LtiConstants::AP_CLAIM_ASSET:
                // case LtiConstants::EULA_CLAIM_SERVICE:
            default:
                throw new \InvalidArgumentException(
                    "Claim type '$claim' is not recognized or not implemented."
                );
        }
    }
}
