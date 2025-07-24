<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\Claims\Concerns\HasScope;

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

    public function hasScope(string $requiredScope): bool
    {
        return in_array($requiredScope, $this->scope());
    }
}
