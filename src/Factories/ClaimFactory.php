<?php

namespace Packback\Lti1p3\Factories;

use Packback\Lti1p3\Claims\Activity;
use Packback\Lti1p3\Claims\Asset;
use Packback\Lti1p3\Claims\AssetReport;
use Packback\Lti1p3\Claims\AssetReportType;
use Packback\Lti1p3\Claims\AssetService;
use Packback\Lti1p3\Claims\AssignmentGradeService;
use Packback\Lti1p3\Claims\Claim;
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
use Packback\Lti1p3\Messages\LtiMessage;

class ClaimFactory
{
    use Claimable;

    public static function create(string $claim, LtiMessage $message): Claim
    {
        switch ($claim) {
            case Claim::NOTICE:
                return new Notice(static::getClaimFrom($claim, $message->getBody()));
            case Claim::ACTIVITY:
                return new Activity(static::getClaimFrom($claim, $message->getBody()));
            case Claim::PLATFORMNOTIFICATIONSERVICE:
                return new PlatformNotificationService(static::getClaimFrom($claim, $message->getBody()));
            case Claim::DL_DEEP_LINK_SETTINGS:
                return new DeepLinkSettings(static::getClaimFrom($claim, $message->getBody()));
            case Claim::DEPLOYMENT_ID:
                return new DeploymentId(static::getClaimFrom($claim, $message->getBody()));
            case Claim::MESSAGE_TYPE:
                return new MessageType(static::getClaimFrom($claim, $message->getBody()));
            case Claim::VERSION:
                return new Version(static::getClaimFrom($claim, $message->getBody()));
            case Claim::ROLES:
                return new Roles(static::getClaimFrom($claim, $message->getBody()));
            case Claim::FOR_USER:
                return new ForUser(static::getClaimFrom($claim, $message->getBody()));
            case Claim::TARGET_LINK_URI:
                return new TargetLinkUri(static::getClaimFrom($claim, $message->getBody()));
            case Claim::RESOURCE_LINK:
                return new ResourceLink(static::getClaimFrom($claim, $message->getBody()));
            case Claim::CONTEXT:
                return new Context(static::getClaimFrom($claim, $message->getBody()));
            case Claim::CUSTOM:
                return new Custom(static::getClaimFrom($claim, $message->getBody()));
            case Claim::LAUNCH_PRESENTATION:
                return new LaunchPresentation(static::getClaimFrom($claim, $message->getBody()));
            case Claim::LIS:
                return new Lis(static::getClaimFrom($claim, $message->getBody()));
            case Claim::LTI1P1:
                return new Lti1p1(static::getClaimFrom($claim, $message->getBody()));
            case Claim::ROLE_SCOPE_MENTOR:
                return new RoleScopeMentor(static::getClaimFrom($claim, $message->getBody()));
            case Claim::TOOL_PLATFORM:
                return new ToolPlatform(static::getClaimFrom($claim, $message->getBody()));
            case Claim::DL_CONTENT_ITEMS:
                return new ContentItems(static::getClaimFrom($claim, $message->getBody()));
            case Claim::DL_DATA:
                return new Data(static::getClaimFrom($claim, $message->getBody()));
            case Claim::NRPS_NAMESROLESSERVICE:
                return new NamesRoleProvisioningService(static::getClaimFrom($claim, $message->getBody()));
            case Claim::AGS_ENDPOINT:
                return new AssignmentGradeService(static::getClaimFrom($claim, $message->getBody()));
            case Claim::GS_GROUPSSERVICE:
                return new GroupService(static::getClaimFrom($claim, $message->getBody()));
            case Claim::ASSETREPORT:
                return new AssetReport(static::getClaimFrom($claim, $message->getBody()));
            case Claim::ASSETREPORT_TYPE:
                return new AssetReportType(static::getClaimFrom($claim, $message->getBody()));
            case Claim::ASSETSERVICE:
                return new AssetService(static::getClaimFrom($claim, $message->getBody()));
            case Claim::ASSETREPORT:
                return new Report(static::getClaimFrom($claim, $message->getBody()));
            case Claim::SUBMISSION:
                return new Submission(static::getClaimFrom($claim, $message->getBody()));
            case Claim::ASSETREPORT_TYPE:
                return new ReportType(static::getClaimFrom($claim, $message->getBody()));
            case Claim::ASSET:
                return new Asset(static::getClaimFrom($claim, $message->getBody()));
            case Claim::EULASERVICE:
                return new EulaService(static::getClaimFrom($claim, $message->getBody()));
            default:
                throw new \InvalidArgumentException(
                    "Claim type '$claim' is not recognized or not implemented."
                );
        }
    }
}
