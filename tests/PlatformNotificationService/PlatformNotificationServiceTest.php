<?php

namespace Tests\PlatformNotificationService;

use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\PlatformNotificationService\PlatformNotificationService;
use Tests\TestCase;

class PlatformNotificationServiceTest extends TestCase
{
    private PlatformNotificationService $service;
    private array $testSettings;

    protected function setUp(): void
    {
        $this->testSettings = [
            'platform_notification_service_url' => 'https://example.com/pns',
            'service_versions' => ['1.0', '2.0'],
            'scope' => [
                'https://purl.imsglobal.org/spec/lti-pns/scope/notice',
                'https://purl.imsglobal.org/spec/lti-pns/scope/notice.readonly',
            ],
            'notice_types_supported' => [
                LtiConstants::NOTICE_TYPE_HELLOWORLD,
                LtiConstants::NOTICE_TYPE_CONTEXTCOPY,
                LtiConstants::NOTICE_TYPE_ASSETPROCESSORSUBMISSION,
            ],
        ];

        $this->service = new PlatformNotificationService($this->testSettings);
    }

    public function test_it_instantiates()
    {
        $this->assertInstanceOf(PlatformNotificationService::class, $this->service);
    }

    public function test_it_gets_settings()
    {
        $result = $this->service->settings();
        $this->assertEquals($this->testSettings, $result);
    }

    public function test_it_gets_platform_notification_service_url()
    {
        $result = $this->service->platformNotificationServiceUrl();
        $this->assertEquals('https://example.com/pns', $result);
    }

    public function test_it_gets_service_versions()
    {
        $result = $this->service->serviceVersions();
        $this->assertEquals(['1.0', '2.0'], $result);
    }

    public function test_it_gets_scope()
    {
        $result = $this->service->scope();
        $expected = [
            'https://purl.imsglobal.org/spec/lti-pns/scope/notice',
            'https://purl.imsglobal.org/spec/lti-pns/scope/notice.readonly',
        ];
        $this->assertEquals($expected, $result);
    }

    public function test_it_gets_notice_types_supported()
    {
        $result = $this->service->noticeTypesSupported();
        $expected = [
            LtiConstants::NOTICE_TYPE_HELLOWORLD,
            LtiConstants::NOTICE_TYPE_CONTEXTCOPY,
            LtiConstants::NOTICE_TYPE_ASSETPROCESSORSUBMISSION,
        ];
        $this->assertEquals($expected, $result);
    }

    public function test_it_supports_lti_notice_types()
    {
        $this->assertTrue($this->service->supportsNoticeType(LtiConstants::NOTICE_TYPE_HELLOWORLD));
        $this->assertTrue($this->service->supportsNoticeType(LtiConstants::NOTICE_TYPE_CONTEXTCOPY));
        $this->assertTrue($this->service->supportsNoticeType(LtiConstants::NOTICE_TYPE_ASSETPROCESSORSUBMISSION));
    }

    public function test_it_does_not_support_unknown_notice_type()
    {
        $this->assertFalse($this->service->supportsNoticeType('UnknownNoticeType'));
        $this->assertFalse($this->service->supportsNoticeType(''));
        $this->assertFalse($this->service->supportsNoticeType('NonExistentType'));
    }

    public function test_it_has_pns_scopes()
    {
        $this->assertTrue($this->service->hasScope('https://purl.imsglobal.org/spec/lti-pns/scope/notice'));
        $this->assertTrue($this->service->hasScope('https://purl.imsglobal.org/spec/lti-pns/scope/notice.readonly'));
    }

    public function test_it_does_not_have_unknown_scope()
    {
        $this->assertFalse($this->service->hasScope('https://example.com/unknown/scope'));
        $this->assertFalse($this->service->hasScope(''));
        $this->assertFalse($this->service->hasScope('invalid-scope'));
    }

    public function test_it_works_with_empty_settings()
    {
        $emptySettings = [
            'platform_notification_service_url' => '',
            'service_versions' => [],
            'scope' => [],
            'notice_types_supported' => [],
        ];

        $service = new PlatformNotificationService($emptySettings);

        $this->assertEquals('', $service->platformNotificationServiceUrl());
        $this->assertEquals([], $service->serviceVersions());
        $this->assertEquals([], $service->scope());
        $this->assertEquals([], $service->noticeTypesSupported());
        $this->assertFalse($service->supportsNoticeType(LtiConstants::NOTICE_TYPE_HELLOWORLD));
        $this->assertFalse($service->hasScope('https://purl.imsglobal.org/spec/lti-pns/scope/notice'));
    }
}
