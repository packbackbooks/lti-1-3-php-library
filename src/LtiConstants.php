<?php

namespace Packback\Lti1p3;

use Packback\Lti1p3\Claims\Claim;

class LtiConstants
{
    public const V1_3 = '1.3.0';

    // Required message claims
    #[\Deprecated(message: 'use Packback\Lti1p3\Claims\Claim::VERSION instead', since: '6.4')]
    public const VERSION = Claim::VERSION;

    #[\Deprecated(message: 'use Packback\Lti1p3\Claims\Claim::DEPLOYMENT_ID instead', since: '6.4')]
    public const DEPLOYMENT_ID = Claim::DEPLOYMENT_ID;

    #[\Deprecated(message: 'use Packback\Lti1p3\Claims\Claim::ROLES instead', since: '6.4')]
    public const ROLES = Claim::ROLES;

    #[\Deprecated(message: 'use Packback\Lti1p3\Claims\Claim::FOR_USER instead', since: '6.4')]
    public const FOR_USER = Claim::FOR_USER;

    // Required resource link claims
    #[\Deprecated(message: 'use Packback\Lti1p3\Claims\Claim::MESSAGE_TYPE instead', since: '6.4')]
    public const MESSAGE_TYPE = Claim::MESSAGE_TYPE;

    #[\Deprecated(message: 'use Packback\Lti1p3\Claims\Claim::TARGET_LINK_URI instead', since: '6.4')]
    public const TARGET_LINK_URI = Claim::TARGET_LINK_URI;

    #[\Deprecated(message: 'use Packback\Lti1p3\Claims\Claim::RESOURCE_LINK instead', since: '6.4')]
    public const RESOURCE_LINK = Claim::RESOURCE_LINK;

    // Optional message claims
    #[\Deprecated(message: 'use Packback\Lti1p3\Claims\Claim::CONTEXT instead', since: '6.4')]
    public const CONTEXT = Claim::CONTEXT;

    #[\Deprecated(message: 'use Packback\Lti1p3\Claims\Claim::CUSTOM instead', since: '6.4')]
    public const CUSTOM = Claim::CUSTOM;

    #[\Deprecated(message: 'use Packback\Lti1p3\Claims\Claim::LAUNCH_PRESENTATION instead', since: '6.4')]
    public const LAUNCH_PRESENTATION = Claim::LAUNCH_PRESENTATION;

    #[\Deprecated(message: 'use Packback\Lti1p3\Claims\Claim::LIS instead', since: '6.4')]
    public const LIS = Claim::LIS;

    #[\Deprecated(message: 'use Packback\Lti1p3\Claims\Claim::LTI1P1 instead', since: '6.4')]
    public const LTI1P1 = Claim::LTI1P1;

    #[\Deprecated(message: 'use Packback\Lti1p3\Claims\Claim::ROLE_SCOPE_MENTOR instead', since: '6.4')]
    public const ROLE_SCOPE_MENTOR = Claim::ROLE_SCOPE_MENTOR;

    #[\Deprecated(message: 'use Packback\Lti1p3\Claims\Claim::TOOL_PLATFORM instead', since: '6.4')]
    public const TOOL_PLATFORM = Claim::TOOL_PLATFORM;

    // LTI DL
    #[\Deprecated(message: 'use Packback\Lti1p3\Claims\Claim::DL_CONTENT_ITEMS instead', since: '6.4')]
    public const DL_CONTENT_ITEMS = Claim::DL_CONTENT_ITEMS;

    #[\Deprecated(message: 'use Packback\Lti1p3\Claims\Claim::DL_DATA instead', since: '6.4')]
    public const DL_DATA = Claim::DL_DATA;

    #[\Deprecated(message: 'use Packback\Lti1p3\Claims\Claim::DL_DEEP_LINK_SETTINGS instead', since: '6.4')]
    public const DL_DEEP_LINK_SETTINGS = Claim::DL_DEEP_LINK_SETTINGS;
    public const DL_RESOURCE_LINK_TYPE = 'ltiResourceLink';

    // LTI NRPS
    #[\Deprecated(message: 'use Packback\Lti1p3\Claims\Claim::NRPS_NAMESROLESSERVICE instead', since: '6.4')]
    public const NRPS_CLAIM_SERVICE = Claim::NRPS_NAMESROLESSERVICE;
    public const NRPS_SCOPE_MEMBERSHIP_READONLY = 'https://purl.imsglobal.org/spec/lti-nrps/scope/contextmembership.readonly';

    // LTI AGS
    #[\Deprecated(message: 'use Packback\Lti1p3\Claims\Claim::AGS_ENDPOINT instead', since: '6.4')]
    public const AGS_CLAIM_ENDPOINT = Claim::AGS_ENDPOINT;
    public const AGS_SCOPE_LINEITEM = 'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem';
    public const AGS_SCOPE_LINEITEM_READONLY = 'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem.readonly';
    public const AGS_SCOPE_RESULT_READONLY = 'https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly';
    public const AGS_SCOPE_SCORE = 'https://purl.imsglobal.org/spec/lti-ags/scope/score';

    // LTI GS
    #[\Deprecated(message: 'use Packback\Lti1p3\Claims\Claim::GS_GROUPSSERVICE instead', since: '6.4')]
    public const GS_CLAIM_SERVICE = Claim::GS_GROUPSSERVICE;

    // User Vocab
    public const SYSTEM_ADMINISTRATOR = 'http://purl.imsglobal.org/vocab/lis/v2/system/person#Administrator';
    public const SYSTEM_NONE = 'http://purl.imsglobal.org/vocab/lis/v2/system/person#None';
    public const SYSTEM_ACCOUNTADMIN = 'http://purl.imsglobal.org/vocab/lis/v2/system/person#AccountAdmin';
    public const SYSTEM_CREATOR = 'http://purl.imsglobal.org/vocab/lis/v2/system/person#Creator';
    public const SYSTEM_SYSADMIN = 'http://purl.imsglobal.org/vocab/lis/v2/system/person#SysAdmin';
    public const SYSTEM_SYSSUPPORT = 'http://purl.imsglobal.org/vocab/lis/v2/system/person#SysSupport';
    public const SYSTEM_USER = 'http://purl.imsglobal.org/vocab/lis/v2/system/person#User';
    public const INSTITUTION_ADMINISTRATOR = 'http://purl.imsglobal.org/vocab/lis/v2/institution/person#Administrator';
    public const INSTITUTION_FACULTY = 'http://purl.imsglobal.org/vocab/lis/v2/institution/person#Faculty';
    public const INSTITUTION_GUEST = 'http://purl.imsglobal.org/vocab/lis/v2/institution/person#Guest';
    public const INSTITUTION_NONE = 'http://purl.imsglobal.org/vocab/lis/v2/institution/person#None';
    public const INSTITUTION_OTHER = 'http://purl.imsglobal.org/vocab/lis/v2/institution/person#Other';
    public const INSTITUTION_STAFF = 'http://purl.imsglobal.org/vocab/lis/v2/institution/person#Staff';
    public const INSTITUTION_STUDENT = 'http://purl.imsglobal.org/vocab/lis/v2/institution/person#Student';
    public const INSTITUTION_ALUMNI = 'http://purl.imsglobal.org/vocab/lis/v2/institution/person#Alumni';
    public const INSTITUTION_INSTRUCTOR = 'http://purl.imsglobal.org/vocab/lis/v2/institution/person#Instructor';
    public const INSTITUTION_LEARNER = 'http://purl.imsglobal.org/vocab/lis/v2/institution/person#Learner';
    public const INSTITUTION_MEMBER = 'http://purl.imsglobal.org/vocab/lis/v2/institution/person#Member';
    public const INSTITUTION_MENTOR = 'http://purl.imsglobal.org/vocab/lis/v2/institution/person#Mentor';
    public const INSTITUTION_OBSERVER = 'http://purl.imsglobal.org/vocab/lis/v2/institution/person#Observer';
    public const INSTITUTION_PROSPECTIVESTUDENT = 'http://purl.imsglobal.org/vocab/lis/v2/institution/person#ProspectiveStudent';
    public const MEMBERSHIP_ADMINISTRATOR = 'http://purl.imsglobal.org/vocab/lis/v2/membership#Administrator';
    public const MEMBERSHIP_CONTENTDEVELOPER = 'http://purl.imsglobal.org/vocab/lis/v2/membership#ContentDeveloper';
    public const MEMBERSHIP_INSTRUCTOR = 'http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor';
    public const MEMBERSHIP_LEARNER = 'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner';
    public const MEMBERSHIP_MENTOR = 'http://purl.imsglobal.org/vocab/lis/v2/membership#Mentor';
    public const MEMBERSHIP_MANAGER = 'http://purl.imsglobal.org/vocab/lis/v2/membership#Manager';
    public const MEMBERSHIP_MEMBER = 'http://purl.imsglobal.org/vocab/lis/v2/membership#Member';
    public const MEMBERSHIP_OFFICER = 'http://purl.imsglobal.org/vocab/lis/v2/membership#Officer';

    // Context sub-roles
    public const MEMBERSHIP_EXTERNALINSTRUCTOR = 'http://purl.imsglobal.org/vocab/lis/v2/membership/Instructor#ExternalInstructor';
    public const MEMBERSHIP_GRADER = 'http://purl.imsglobal.org/vocab/lis/v2/membership/Instructor#Grader';
    public const MEMBERSHIP_GUESTINSTRUCTOR = 'http://purl.imsglobal.org/vocab/lis/v2/membership/Instructor#GuestInstructor';
    public const MEMBERSHIP_LECTURER = 'http://purl.imsglobal.org/vocab/lis/v2/membership/Instructor#Lecturer';
    public const MEMBERSHIP_PRIMARYINSTRUCTOR = 'http://purl.imsglobal.org/vocab/lis/v2/membership/Instructor#PrimaryInstructor';
    public const MEMBERSHIP_SECONDARYINSTRUCTOR = 'http://purl.imsglobal.org/vocab/lis/v2/membership/Instructor#SecondaryInstructor';
    public const MEMBERSHIP_TA = 'http://purl.imsglobal.org/vocab/lis/v2/membership/Instructor#TeachingAssistant';
    public const MEMBERSHIP_TAGROUP = 'http://purl.imsglobal.org/vocab/lis/v2/membership/Instructor#TeachingAssistantGroup';
    public const MEMBERSHIP_TAOFFERING = 'http://purl.imsglobal.org/vocab/lis/v2/membership/Instructor#TeachingAssistantOffering';
    public const MEMBERSHIP_TASECTION = 'http://purl.imsglobal.org/vocab/lis/v2/membership/Instructor#TeachingAssistantSection';
    public const MEMBERSHIP_TASECTIONASSOCIATION = 'http://purl.imsglobal.org/vocab/lis/v2/membership/Instructor#TeachingAssistantSectionAssociation';
    public const MEMBERSHIP_TATEMPLATE = 'http://purl.imsglobal.org/vocab/lis/v2/membership/Instructor#TeachingAssistantTemplate';

    // Context Vocab
    public const COURSE_TEMPLATE = 'http://purl.imsglobal.org/vocab/lis/v2/course#CourseTemplate';
    public const COURSE_OFFERING = 'http://purl.imsglobal.org/vocab/lis/v2/course#CourseOffering';
    public const COURSE_SECTION = 'http://purl.imsglobal.org/vocab/lis/v2/course#CourseSection';
    public const COURSE_GROUP = 'http://purl.imsglobal.org/vocab/lis/v2/course#Group';

    // Message Types
    public const MESSAGE_TYPE_DEEPLINK = 'LtiDeepLinkingRequest';
    public const MESSAGE_TYPE_DEEPLINK_RESPONSE = 'LtiDeepLinkingResponse';
    public const MESSAGE_TYPE_RESOURCE = 'LtiResourceLinkRequest';
    public const MESSAGE_TYPE_SUBMISSIONREVIEW = 'LtiSubmissionReviewRequest';
    public const MESSAGE_TYPE_EULA = 'LtiEulaRequest';
    public const MESSAGE_TYPE_REPORTREVIEW = 'LtiReportReviewRequest';
    public const MESSAGE_TYPE_ASSETPROCESSORSETTINGS = 'LtiAssetProcessorSettingsRequest';

    // Notice Types
    public const NOTICE_TYPE_HELLOWORLD = 'LtiHelloWorldNotice';
    public const NOTICE_TYPE_CONTEXTCOPY = 'LtiContextCopyNotice';
    public const NOTICE_TYPE_ASSETPROCESSORSUBMISSION = 'LtiAssetProcessorSubmissionNotice';
}
