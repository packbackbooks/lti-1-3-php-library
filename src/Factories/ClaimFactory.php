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
                static::createNotice($message);
            case Claim::ACTIVITY:
                static::createActivity($message);
            case Claim::PLATFORMNOTIFICATIONSERVICE:
                static::createPlatformNotificationService($message);
            case Claim::DL_DEEP_LINK_SETTINGS:
                static::createDeepLinkSettings($message);
            case Claim::DEPLOYMENT_ID:
                static::createDeploymentId($message);
            case Claim::MESSAGE_TYPE:
                static::createMessageType($message);
            case Claim::VERSION:
                static::createVersion($message);
            case Claim::ROLES:
                static::createRoles($message);
            case Claim::FOR_USER:
                static::createForUser($message);
            case Claim::TARGET_LINK_URI:
                static::createTargetLinkUri($message);
            case Claim::RESOURCE_LINK:
                static::createResourceLink($message);
            case Claim::CONTEXT:
                static::createContext($message);
            case Claim::CUSTOM:
                static::createCustom($message);
            case Claim::LAUNCH_PRESENTATION:
                static::createLaunchPresentation($message);
            case Claim::LIS:
                static::createLis($message);
            case Claim::LTI1P1:
                static::createLti1p1($message);
            case Claim::ROLE_SCOPE_MENTOR:
                static::createRoleScopeMentor($message);
            case Claim::TOOL_PLATFORM:
                static::createToolPlatform($message);
            case Claim::DL_CONTENT_ITEMS:
                static::createContentItems($message);
            case Claim::DL_DATA:
                static::createData($message);
            case Claim::NRPS_NAMESROLESSERVICE:
                static::createNamesRoleProvisioningService($message);
            case Claim::AGS_ENDPOINT:
                static::createAssignmentGradeService($message);
            case Claim::GS_GROUPSSERVICE:
                static::createGroupService($message);
            case Claim::ASSETREPORT:
                static::createAssetReport($message);
            case Claim::ASSETREPORT_TYPE:
                static::createAssetReportType($message);
            case Claim::ASSETSERVICE:
                static::createAssetService($message);
            case Claim::ASSETREPORT:
                static::createReport($message);
            case Claim::SUBMISSION:
                static::createSubmission($message);
            case Claim::ASSETREPORT_TYPE:
                static::createReportType($message);
            case Claim::ASSET:
                static::createAsset($message);
            case Claim::EULASERVICE:
                static::createEulaService($message);
            default:
                throw new \InvalidArgumentException(
                    "Claim type '$claim' is not recognized or not implemented."
                );
        }
    }

    public static function createNotice(LtiMessage $message): Notice
    {
        return new Notice(static::getClaimFrom(Claim::NOTICE, $message->getBody()));
    }

    public static function createActivity(LtiMessage $message): Activity
    {
        return new Activity(static::getClaimFrom(Claim::ACTIVITY, $message->getBody()));
    }

    public static function createPlatformNotificationService(LtiMessage $message): PlatformNotificationService
    {
        return new PlatformNotificationService(static::getClaimFrom(Claim::PLATFORMNOTIFICATIONSERVICE, $message->getBody()));
    }

    public static function createDeepLinkSettings(LtiMessage $message): DeepLinkSettings
    {
        return new DeepLinkSettings(static::getClaimFrom(Claim::DL_DEEP_LINK_SETTINGS, $message->getBody()));
    }

    public static function createDeploymentId(LtiMessage $message): DeploymentId
    {
        return new DeploymentId(static::getClaimFrom(Claim::DEPLOYMENT_ID, $message->getBody()));
    }

    public static function createMessageType(LtiMessage $message): MessageType
    {
        return new MessageType(static::getClaimFrom(Claim::MESSAGE_TYPE, $message->getBody()));
    }

    public static function createVersion(LtiMessage $message): Version
    {
        return new Version(static::getClaimFrom(Claim::VERSION, $message->getBody()));
    }

    public static function createRoles(LtiMessage $message): Roles
    {
        return new Roles(static::getClaimFrom(Claim::ROLES, $message->getBody()));
    }

    public static function createForUser(LtiMessage $message): ForUser
    {
        return new ForUser(static::getClaimFrom(Claim::FOR_USER, $message->getBody()));
    }

    public static function createTargetLinkUri(LtiMessage $message): TargetLinkUri
    {
        return new TargetLinkUri(static::getClaimFrom(Claim::TARGET_LINK_URI, $message->getBody()));
    }

    public static function createResourceLink(LtiMessage $message): ResourceLink
    {
        return new ResourceLink(static::getClaimFrom(Claim::RESOURCE_LINK, $message->getBody()));
    }

    public static function createContext(LtiMessage $message): Context
    {
        return new Context(static::getClaimFrom(Claim::CONTEXT, $message->getBody()));
    }

    public static function createCustom(LtiMessage $message): Custom
    {
        return new Custom(static::getClaimFrom(Claim::CUSTOM, $message->getBody()));
    }

    public static function createLaunchPresentation(LtiMessage $message): LaunchPresentation
    {
        return new LaunchPresentation(static::getClaimFrom(Claim::LAUNCH_PRESENTATION, $message->getBody()));
    }

    public static function createLis(LtiMessage $message): Lis
    {
        return new Lis(static::getClaimFrom(Claim::LIS, $message->getBody()));
    }

    public static function createLti1p1(LtiMessage $message): Lti1p1
    {
        return new Lti1p1(static::getClaimFrom(Claim::LTI1P1, $message->getBody()));
    }

    public static function createRoleScopeMentor(LtiMessage $message): RoleScopeMentor
    {
        return new RoleScopeMentor(static::getClaimFrom(Claim::ROLE_SCOPE_MENTOR, $message->getBody()));
    }

    public static function createToolPlatform(LtiMessage $message): ToolPlatform
    {
        return new ToolPlatform(static::getClaimFrom(Claim::TOOL_PLATFORM, $message->getBody()));
    }

    public static function createContentItems(LtiMessage $message): ContentItems
    {
        return new ContentItems(static::getClaimFrom(Claim::DL_CONTENT_ITEMS, $message->getBody()));
    }

    public static function createData(LtiMessage $message): Data
    {
        return new Data(static::getClaimFrom(Claim::DL_DATA, $message->getBody()));
    }

    public static function createNamesRoleProvisioningService(LtiMessage $message): NamesRoleProvisioningService
    {
        return new NamesRoleProvisioningService(static::getClaimFrom(Claim::NRPS_NAMESROLESSERVICE, $message->getBody()));
    }

    public static function createAssignmentGradeService(LtiMessage $message): AssignmentGradeService
    {
        return new AssignmentGradeService(static::getClaimFrom(Claim::AGS_ENDPOINT, $message->getBody()));
    }

    public static function createGroupService(LtiMessage $message): GroupService
    {
        return new GroupService(static::getClaimFrom(Claim::GS_GROUPSSERVICE, $message->getBody()));
    }

    public static function createAssetReport(LtiMessage $message): AssetReport
    {
        return new AssetReport(static::getClaimFrom(Claim::ASSETREPORT, $message->getBody()));
    }

    public static function createAssetReportType(LtiMessage $message): AssetReportType
    {
        return new AssetReportType(static::getClaimFrom(Claim::ASSETREPORT_TYPE, $message->getBody()));
    }

    public static function createAssetService(LtiMessage $message): AssetService
    {
        return new AssetService(static::getClaimFrom(Claim::ASSETSERVICE, $message->getBody()));
    }

    public static function createReport(LtiMessage $message): Report
    {
        return new Report(static::getClaimFrom(Claim::ASSETREPORT, $message->getBody()));
    }

    public static function createSubmission(LtiMessage $message): Submission
    {
        return new Submission(static::getClaimFrom(Claim::SUBMISSION, $message->getBody()));
    }

    public static function createReportType(LtiMessage $message): ReportType
    {
        return new ReportType(static::getClaimFrom(Claim::ASSETREPORT_TYPE, $message->getBody()));
    }

    public static function createAsset(LtiMessage $message): Asset
    {
        return new Asset(static::getClaimFrom(Claim::ASSET, $message->getBody()));
    }

    public static function createEulaService(LtiMessage $message): EulaService
    {
        return new EulaService(static::getClaimFrom(Claim::EULASERVICE, $message->getBody()));
    }

}
