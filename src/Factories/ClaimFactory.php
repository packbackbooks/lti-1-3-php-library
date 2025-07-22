<?php

namespace Packback\Lti1p3\Factories;

use Packback\Lti1p3\Claims\Activity;
use Packback\Lti1p3\Claims\Asset;
use Packback\Lti1p3\Claims\AssetProcessorService;
use Packback\Lti1p3\Claims\AssignmentGradeService;
use Packback\Lti1p3\Claims\ContentItems;
use Packback\Lti1p3\Claims\Context;
use Packback\Lti1p3\Claims\Custom;
use Packback\Lti1p3\Claims\Data;
use Packback\Lti1p3\Claims\DeepLinkSettings;
use Packback\Lti1p3\Claims\DeploymentId;
use Packback\Lti1p3\Claims\EulaService;
use Packback\Lti1p3\Claims\ForUser;
use Packback\Lti1p3\Claims\GroupService;
use Packback\Lti1p3\Claims\LaunchPresentation;
use Packback\Lti1p3\Claims\Lis;
use Packback\Lti1p3\Claims\Lti1p1;
use Packback\Lti1p3\Claims\MessageType;
use Packback\Lti1p3\Claims\NamesRoleProvisioningService;
use Packback\Lti1p3\Claims\Notice;
use Packback\Lti1p3\Claims\PlatformNotificationService;
use Packback\Lti1p3\Claims\Report;
use Packback\Lti1p3\Claims\ReportType;
use Packback\Lti1p3\Claims\ResourceLink;
use Packback\Lti1p3\Claims\Roles;
use Packback\Lti1p3\Claims\RoleScopeMentor;
use Packback\Lti1p3\Claims\Submission;
use Packback\Lti1p3\Claims\TargetLinkUri;
use Packback\Lti1p3\Claims\ToolPlatform;
use Packback\Lti1p3\Claims\Version;
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
            case LtiConstants::VERSION:
                return new Version(static::getClaimFrom($claim, $message->getBody()));
            case LtiConstants::ROLES:
                return new Roles(static::getClaimFrom($claim, $message->getBody()));
            case LtiConstants::FOR_USER:
                return new ForUser(static::getClaimFrom($claim, $message->getBody()));
            case LtiConstants::TARGET_LINK_URI:
                return new TargetLinkUri(static::getClaimFrom($claim, $message->getBody()));
            case LtiConstants::RESOURCE_LINK:
                return new ResourceLink(static::getClaimFrom($claim, $message->getBody()));
            case LtiConstants::CONTEXT:
                return new Context(static::getClaimFrom($claim, $message->getBody()));
            case LtiConstants::CUSTOM:
                return new Custom(static::getClaimFrom($claim, $message->getBody()));
            case LtiConstants::LAUNCH_PRESENTATION:
                return new LaunchPresentation(static::getClaimFrom($claim, $message->getBody()));
            case LtiConstants::LIS:
                return new Lis(static::getClaimFrom($claim, $message->getBody()));
            case LtiConstants::LTI1P1:
                return new Lti1p1(static::getClaimFrom($claim, $message->getBody()));
            case LtiConstants::ROLE_SCOPE_MENTOR:
                return new RoleScopeMentor(static::getClaimFrom($claim, $message->getBody()));
            case LtiConstants::TOOL_PLATFORM:
                return new ToolPlatform(static::getClaimFrom($claim, $message->getBody()));
            case LtiConstants::DL_CONTENT_ITEMS:
                return new ContentItems(static::getClaimFrom($claim, $message->getBody()));
            case LtiConstants::DL_DATA:
                return new Data(static::getClaimFrom($claim, $message->getBody()));
            case LtiConstants::NRPS_CLAIM_SERVICE:
                return new NamesRoleProvisioningService(static::getClaimFrom($claim, $message->getBody()));
            case LtiConstants::AGS_CLAIM_ENDPOINT:
                return new AssignmentGradeService(static::getClaimFrom($claim, $message->getBody()));
            case LtiConstants::GS_CLAIM_SERVICE:
                return new GroupService(static::getClaimFrom($claim, $message->getBody()));
            case LtiConstants::AP_CLAIM_SERVICE:
                return new AssetProcessorService(static::getClaimFrom($claim, $message->getBody()));
            case LtiConstants::AP_CLAIM_REPORT:
                return new Report(static::getClaimFrom($claim, $message->getBody()));
            case LtiConstants::AP_CLAIM_SUBMISSION:
                return new Submission(static::getClaimFrom($claim, $message->getBody()));
            case LtiConstants::AP_CLAIM_REPORT_TYPE:
                return new ReportType(static::getClaimFrom($claim, $message->getBody()));
            case LtiConstants::AP_CLAIM_ASSET:
                return new Asset(static::getClaimFrom($claim, $message->getBody()));
            case LtiConstants::EULA_CLAIM_SERVICE:
                return new EulaService(static::getClaimFrom($claim, $message->getBody()));
            default:
                throw new \InvalidArgumentException(
                    "Claim type '$claim' is not recognized or not implemented."
                );
        }
    }
}
