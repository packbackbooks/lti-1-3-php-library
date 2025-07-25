<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\Claims\Concerns\HasScope;

/**
 * PlatformNotificationService Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti/claim/platformnotificationservice
 *
 * Example payload:
 * {
 *     "https://purl.imsglobal.org/spec/lti/claim/platformnotificationservice": {
 *         "platform_notification_service_url": "https://www.myuniv.org/lti-services/platformNotices",
 *         "service_versions": ["1.0"],
 *         "notice_types_supported": [
 *             "LtiAssetProcessorSubmissionNotice"
 *         ]
 *     }
 * }
 */
class PlatformNotificationService extends Claim
{
    use HasScope;

    public static function claimKey(): string
    {
        return Claim::PLATFORMNOTIFICATIONSERVICE;
    }

    public function platformNotificationServiceUrl(): string
    {
        return $this->getBody()['platform_notification_service_url'];
    }

    public function serviceVersions(): array
    {
        return $this->getBody()['service_versions'];
    }

    public function noticeTypesSupported(): array
    {
        return $this->getBody()['notice_types_supported'];
    }

    public function supportsNoticeType(string $noticeType): bool
    {
        return in_array($noticeType, $this->noticeTypesSupported());
    }
}
