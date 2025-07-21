<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\LtiConstants;

class PlatformNotificationService extends Claim
{
    public static function key(): string
    {
        return LtiConstants::PNS_CLAIM_SERVICE;
    }

    public function platformNotificationServiceUrl(): string
    {
        return $this->getBody()['platform_notification_service_url'];
    }

    public function serviceVersions(): array
    {
        return $this->getBody()['service_versions'];
    }

    public function scope(): array
    {
        return $this->getBody()['scope'];
    }

    public function noticeTypesSupported(): array
    {
        return $this->getBody()['notice_types_supported'];
    }

    public function supportsNoticeType(string $noticeType): bool
    {
        return in_array($noticeType, $this->noticeTypesSupported());
    }

    public function hasScope(string $requiredScope): bool
    {
        return in_array($requiredScope, $this->scope());
    }
}
