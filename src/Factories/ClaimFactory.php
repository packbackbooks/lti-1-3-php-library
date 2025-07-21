<?php

namespace Packback\Lti1p3\Factories;

use Packback\Lti1p3\Claims\Activity;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\DeepLinkSettings;
use Packback\Lti1p3\Claims\DeploymentId;
use Packback\Lti1p3\Claims\MessageType;
use Packback\Lti1p3\Claims\Notice;
use Packback\Lti1p3\Claims\PlatformNotificationService;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\Messages\LtiMessage;

abstract class ClaimFactory
{
    public static function create(string $claim, LtiMessage $message)
    {
        $class = static::getClaimClass($claim);

        switch ($claim) {
            case LtiConstants::PNS_CLAIM_NOTICE:
                return new Notice($message->getBody());
            case LtiConstants::AP_CLAIM_ACTIVITY:
                return new Activity($message->getBody());
            case LtiConstants::PNS_CLAIM_SERVICE:
                return new PlatformNotificationService($message->getBody());
            case LtiConstants::DL_DEEP_LINK_SETTINGS:
                return new DeepLinkSettings($message->getBody());
            case LtiConstants::DEPLOYMENT_ID:
                return new DeploymentId($message->getBody());
            case LtiConstants::MESSAGE_TYPE:
                return new MessageType($message->getBody());
            default:
                throw new \InvalidArgumentException(
                    "Claim type '$claim' is not recognized or not implemented."
                );
        }
    }

    public static function getClaimClass(string $claim): string
    {
        $typeClaimMap = [
            // LtiConstants::VERSION => ::class,
            LtiConstants::DEPLOYMENT_ID => DeploymentId::class,
            // LtiConstants::ROLES => ::class,
            // LtiConstants::FOR_USER => ::class,
            // LtiConstants::MESSAGE_TYPE => ::class,
            // LtiConstants::TARGET_LINK_URI => ::class,
            // LtiConstants::RESOURCE_LINK => ::class,
            // LtiConstants::CONTEXT => ::class,
            // LtiConstants::CUSTOM => ::class,
            // LtiConstants::LAUNCH_PRESENTATION => ::class,
            // LtiConstants::LIS => ::class,
            // LtiConstants::LTI1P1 => ::class,
            // LtiConstants::ROLE_SCOPE_MENTOR => ::class,
            // LtiConstants::TOOL_PLATFORM => ::class,
            // LtiConstants::DL_CONTENT_ITEMS => ::class,
            // LtiConstants::DL_DATA => ::class,
            LtiConstants::DL_DEEP_LINK_SETTINGS => DeepLinkSettings::class,
            // LtiConstants::NRPS_CLAIM_SERVICE => ::class,
            // LtiConstants::AGS_CLAIM_ENDPOINT => ::class,
            // LtiConstants::GS_CLAIM_SERVICE => ::class,
            LtiConstants::PNS_CLAIM_SERVICE => PlatformNotificationService::class,
            // LtiConstants::PNS_CLAIM_NOTICE => ::class,
            // LtiConstants::AP_CLAIM_SERVICE => ::class,
            // LtiConstants::AP_CLAIM_REPORT => ::class,
            LtiConstants::AP_CLAIM_ACTIVITY => Activity::class,
            // LtiConstants::AP_CLAIM_SUBMISSION => ::class,
            // LtiConstants::AP_CLAIM_REPORT_TYPE => ::class,
            // LtiConstants::AP_CLAIM_ASSET => ::class,
            // LtiConstants::EULA_CLAIM_SERVICE => ::class,
        ];

        return $typeClaimMap[$claim];
    }
}
