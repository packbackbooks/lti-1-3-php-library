<?php

namespace IMSGlobal\LTI;

class LTI_Constants
{
    public const V1_3 = '1.3.0';

    // Required message claims
    public const MESSAGE_TYPE = 'https://purl.imsglobal.org/spec/lti/claim/message_type';
    public const VERSION = 'https://purl.imsglobal.org/spec/lti/claim/version';
    public const DEPLOYMENT_ID = 'https://purl.imsglobal.org/spec/lti/claim/deployment_id';
    public const TARGET_LINK_URI = 'https://purl.imsglobal.org/spec/lti/claim/target_link_uri';
    public const RESOURCE_LINK = 'https://purl.imsglobal.org/spec/lti/claim/resource_link';
    public const ROLES = 'https://purl.imsglobal.org/spec/lti/claim/roles';

    // Optional message claims
    public const CONTEXT = 'https://purl.imsglobal.org/spec/lti/claim/context';
    public const TOOL_PLATFORM = 'https://purl.imsglobal.org/spec/lti/claim/tool_platform';
    public const ROLE_SCOPE_MENTOR = 'https://purlimsglobal.org/spec/lti/claim/role_scope_mentor';
    public const LAUNCH_PRESENTATION = 'https://purl.imsglobal.org/spec/lti/claim/launch_presentation';
    public const LIS = 'https://purl.imsglobal.org/spec/lti/claim/lis';
    public const CUSTOM = 'https://purl.imsglobal.org/spec/lti/claim/custom';

    // LTI DL
    public const DL_CONTENT_ITEMS = 'https://purl.imsglobal.org/spec/lti-dl/claim/content_items';
    public const DL_DATA = 'https://purl.imsglobal.org/spec/lti-dl/claim/data';
    public const DL_DEEP_LINK_SETTINGS = 'https://purl.imsglobal.org/spec/lti-dl/claim/deep_linking_settings';

    // LTI NRPS
    public const NRPS_NAMESROLESPROVISIONINGSERVICE = 'https://purl.imsglobal.org/spec/lti-nrps/claim/namesroleservice';
    public const NRPS_CONTEXT_MEMBERSHIP_READ_ONLY = 'https://purl.imsglobal.org/spec/lti-nrps/scope/contextmembership.readonly';

    // LTI AGS
    public const AGS_ENDPOINT = 'https://purl.imsglobal.org/spec/lti-ags/claim/endpoint';
    public const AGS_LINEITEM = 'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem';
    public const AGS_LINEITEM_READONLY = 'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem.readonly';
    public const AGS_RESULT_READONLY = 'https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly';
    public const AGS_SCORE = 'https://purl.imsglobal.org/spec/lti-ags/scope/score';

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

    // Context Vocab
    public const COURSE_TEMPLATE = 'http://purl.imsglobal.org/vocab/lis/v2/course#CourseTemplate';
    public const COURSE_OFFERING = 'http://purl.imsglobal.org/vocab/lis/v2/course#CourseOffering';
    public const COURSE_SECTION = 'http://purl.imsglobal.org/vocab/lis/v2/course#CourseSection';
    public const COURSE_GROUP = 'http://purl.imsglobal.org/vocab/lis/v2/course#Group';

    // Context sub-roles
    public const SUB_ADMINISTRATOR = 'http://purl.imsglobal.org/vocab/lis/v2/membership/Administrator#Administrator';
    public const SUB_DEVELOPER = 'http://purl.imsglobal.org/vocab/lis/v2/membership/Administrator#Developer';
    public const SUB_EXTERNALDEVELOPER = 'http://purl.imsglobal.org/vocab/lis/v2/membership/Administrator#ExternalDeveloper';
    public const SUB_EXTERNALSUPPORT = 'http://purl.imsglobal.org/vocab/lis/v2/membership/Administrator#ExternalSupport';
    public const SUB_EXTERNALSYSTEMADMINISTRATOR = 'http://purl.imsglobal.org/vocab/lis/v2/membership/Administrator#ExternalSystemAdministrator';
    public const SUB_SUPPORT = 'http://purl.imsglobal.org/vocab/lis/v2/membership/Administrator#Support';
    public const SUB_SYSTEMADMINISTRATOR = 'http://purl.imsglobal.org/vocab/lis/v2/membership/Administrator#SystemAdministrator';
    public const SUB_CONTENTDEVELOPER = 'http://purl.imsglobal.org/vocab/lis/v2/membership/ContentDeveloper#ContentDeveloper';
    public const SUB_CONTENTEXPERT = 'http://purl.imsglobal.org/vocab/lis/v2/membership/ContentDeveloper#ContentExpert';
    public const SUB_EXTERNALCONTENTEXPERT = 'http://purl.imsglobal.org/vocab/lis/v2/membership/ContentDeveloper#ExternalContentExpert';
    public const SUB_LIBRARIAN = 'http://purl.imsglobal.org/vocab/lis/v2/membership/ContentDeveloper#Librarian';
    public const SUB_EXTERNALINSTRUCTOR = 'http://purl.imsglobal.org/vocab/lis/v2/membership/Instructor#ExternalInstructor';
    public const SUB_GRADER = 'http://purl.imsglobal.org/vocab/lis/v2/membership/Instructor#Grader';
    public const SUB_GUESTINSTRUCTOR = 'http://purl.imsglobal.org/vocab/lis/v2/membership/Instructor#GuestInstructor';
    public const SUB_LECTURER = 'http://purl.imsglobal.org/vocab/lis/v2/membership/Instructor#Lecturer';
    public const SUB_PRIMARYINSTRUCTOR = 'http://purl.imsglobal.org/vocab/lis/v2/membership/Instructor#PrimaryInstructor';
    public const SUB_SECONDARYINSTRUCTOR = 'http://purl.imsglobal.org/vocab/lis/v2/membership/Instructor#SecondaryInstructor';
    public const SUB_TA = 'http://purl.imsglobal.org/vocab/lis/v2/membership/Instructor#TeachingAssistant';
    public const SUB_TAGROUP = 'http://purl.imsglobal.org/vocab/lis/v2/membership/Instructor#TeachingAssistantGroup';
    public const SUB_TAOFFERING = 'http://purl.imsglobal.org/vocab/lis/v2/membership/Instructor#TeachingAssistantOffering';
    public const SUB_TASECTION = 'http://purl.imsglobal.org/vocab/lis/v2/membership/Instructor#TeachingAssistantSection';
    public const SUB_TASECTIONASSOCIATION = 'http://purl.imsglobal.org/vocab/lis/v2/membership/Instructor#TeachingAssistantSectionAssociation';
    public const SUB_TATEMPLATE = 'http://purl.imsglobal.org/vocab/lis/v2/membership/Instructor#TeachingAssistantTemplate';
    public const SUB_EXTERNALLEARNER = 'http://purl.imsglobal.org/vocab/lis/v2/membership/Learner#ExternalLearner';
    public const SUB_GUESTLEARNER = 'http://purl.imsglobal.org/vocab/lis/v2/membership/Learner#GuestLearner';
    public const SUB_INSTRUCTOR = 'http://purl.imsglobal.org/vocab/lis/v2/membership/Learner#Instructor';
    public const SUB_LEARNER = 'http://purl.imsglobal.org/vocab/lis/v2/membership/Learner#Learner';
    public const SUB_NONCREDITLEARNER = 'http://purl.imsglobal.org/vocab/lis/v2/membership/Learner#NonCreditLearner';
}
