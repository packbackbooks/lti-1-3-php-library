<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\Concerns\Claimable;
use Packback\Lti1p3\Messages\LtiMessage;

abstract class Claim
{
    use Claimable;

    // Basic LTI claims
    public const VERSION = 'https://purl.imsglobal.org/spec/lti/claim/version';
    public const DEPLOYMENT_ID = 'https://purl.imsglobal.org/spec/lti/claim/deployment_id';
    public const ROLES = 'https://purl.imsglobal.org/spec/lti/claim/roles';
    public const FOR_USER = 'https://purl.imsglobal.org/spec/lti/claim/for_user';
    public const MESSAGE_TYPE = 'https://purl.imsglobal.org/spec/lti/claim/message_type';
    public const TARGET_LINK_URI = 'https://purl.imsglobal.org/spec/lti/claim/target_link_uri';
    public const RESOURCE_LINK = 'https://purl.imsglobal.org/spec/lti/claim/resource_link';
    public const CONTEXT = 'https://purl.imsglobal.org/spec/lti/claim/context';
    public const CUSTOM = 'https://purl.imsglobal.org/spec/lti/claim/custom';
    public const LAUNCH_PRESENTATION = 'https://purl.imsglobal.org/spec/lti/claim/launch_presentation';
    public const LIS = 'https://purl.imsglobal.org/spec/lti/claim/lis';
    public const LTI1P1 = 'https://purl.imsglobal.org/spec/lti/claim/lti1p1';
    public const ROLE_SCOPE_MENTOR = 'https://purl.imsglobal.org/spec/lti/claim/role_scope_mentor';
    public const TOOL_PLATFORM = 'https://purl.imsglobal.org/spec/lti/claim/tool_platform';

    // LTI Deep Linking
    public const DL_CONTENT_ITEMS = 'https://purl.imsglobal.org/spec/lti-dl/claim/content_items';
    public const DL_DATA = 'https://purl.imsglobal.org/spec/lti-dl/claim/data';
    public const DL_DEEP_LINK_SETTINGS = 'https://purl.imsglobal.org/spec/lti-dl/claim/deep_linking_settings';

    // LTI NRPS
    public const NRPS_NAMESROLESSERVICE = 'https://purl.imsglobal.org/spec/lti-nrps/claim/namesroleservice';

    // LTI AGS
    public const AGS_ENDPOINT = 'https://purl.imsglobal.org/spec/lti-ags/claim/endpoint';

    // LTI GS
    public const GS_GROUPSSERVICE = 'https://purl.imsglobal.org/spec/lti-gs/claim/groupsservice';

    // Platform Notification Service
    public const PLATFORMNOTIFICATIONSERVICE = 'https://purl.imsglobal.org/spec/lti/claim/platformnotificationservice';
    public const NOTICE = 'https://purl.imsglobal.org/spec/lti/claim/notice';

    // Asset Processor
    public const ASSETSERVICE = 'https://purl.imsglobal.org/spec/lti/claim/assetservice';
    public const ASSETREPORT = 'https://purl.imsglobal.org/spec/lti/claim/assetreport';
    public const ACTIVITY = 'https://purl.imsglobal.org/spec/lti/claim/activity';
    public const SUBMISSION = 'https://purl.imsglobal.org/spec/lti/claim/submission';
    public const ASSETREPORT_TYPE = 'https://purl.imsglobal.org/spec/lti/claim/assetreport_type';
    public const ASSET = 'https://purl.imsglobal.org/spec/lti/claim/asset';

    // EULA Service
    public const EULASERVICE = 'https://purl.imsglobal.org/spec/lti/claim/eulaservice';

    abstract public static function claimKey(): string;

    final public function __construct(
        private $body
    ) {}

    public static function create(LtiMessage $message): static
    {
        return new static(static::getClaimFrom(static::claimKey(), $message->getBody()));
    }

    public function getBody()
    {
        return $this->body;
    }
}
