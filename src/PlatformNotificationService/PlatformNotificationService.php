<?php

namespace Packback\Lti1p3\PlatformNotificationService;

class PlatformNotificationService
{
    public function __construct(
        private array $platform_notification_settings
    ) {}

    public function settings(): array
    {
        return $this->platform_notification_settings;
    }

    public function platformNotificationServiceUrl(): string
    {
        return $this->settings()['platform_notification_service_url'];
    }

    public function serviceVersions(): array
    {
        return $this->settings()['service_versions'];
    }

    public function scope(): array
    {
        return $this->settings()['scope'];
    }

    public function noticeTypesSupported(): array
    {
        return $this->settings()['notice_types_supported'];
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
