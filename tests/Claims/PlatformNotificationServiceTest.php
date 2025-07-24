<?php

namespace Tests\Claims;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\PlatformNotificationService;
use Packback\Lti1p3\Messages\LtiMessage;
use Tests\TestCase;

class PlatformNotificationServiceTest extends TestCase
{
    private $messageMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->messageMock = Mockery::mock(LtiMessage::class);
    }

    public function test_claim_key_returns_platform_notification_service_constant()
    {
        $this->assertEquals(Claim::PLATFORMNOTIFICATIONSERVICE, PlatformNotificationService::claimKey());
    }

    public function test_constructor_sets_body()
    {
        $body = [
            'platform_notification_service_url' => 'https://example.com/notifications',
            'service_versions' => ['1.0'],
            'notice_types_supported' => ['grade_sync'],
            'scope' => ['https://purl.imsglobal.org/spec/lti/scope/notice'],
        ];
        $pns = new PlatformNotificationService($body);

        $this->assertEquals($body, $pns->getBody());
    }

    public function test_create_method_creates_instance_from_message()
    {
        $pnsData = [
            'platform_notification_service_url' => 'https://example.com/notify',
            'service_versions' => ['2.0'],
        ];
        $messageBody = [Claim::PLATFORMNOTIFICATIONSERVICE => $pnsData];
        $this->messageMock->shouldReceive('getBody')->andReturn($messageBody);

        $pns = PlatformNotificationService::create($this->messageMock);

        $this->assertInstanceOf(PlatformNotificationService::class, $pns);
        $this->assertEquals($pnsData, $pns->getBody());
    }

    public function test_platform_notification_service_url_method_returns_url_from_body()
    {
        $url = 'https://example.com/platform-notifications';
        $body = ['platform_notification_service_url' => $url];
        $pns = new PlatformNotificationService($body);

        $this->assertEquals($url, $pns->platformNotificationServiceUrl());
    }

    public function test_service_versions_method_returns_versions_from_body()
    {
        $versions = ['1.0', '2.0'];
        $body = ['service_versions' => $versions];
        $pns = new PlatformNotificationService($body);

        $this->assertEquals($versions, $pns->serviceVersions());
    }

    public function test_notice_types_supported_method_returns_notice_types_from_body()
    {
        $noticeTypes = ['grade_sync', 'submission_review', 'context_copy'];
        $body = ['notice_types_supported' => $noticeTypes];
        $pns = new PlatformNotificationService($body);

        $this->assertEquals($noticeTypes, $pns->noticeTypesSupported());
    }

    public function test_supports_notice_type_method_returns_true_when_supported()
    {
        $body = ['notice_types_supported' => ['grade_sync', 'submission_review']];
        $pns = new PlatformNotificationService($body);

        $this->assertTrue($pns->supportsNoticeType('grade_sync'));
    }

    public function test_supports_notice_type_method_returns_false_when_not_supported()
    {
        $body = ['notice_types_supported' => ['grade_sync']];
        $pns = new PlatformNotificationService($body);

        $this->assertFalse($pns->supportsNoticeType('context_copy'));
    }

    public function test_scope_method_returns_scope_from_body()
    {
        $scope = ['https://purl.imsglobal.org/spec/lti/scope/notice'];
        $body = ['scope' => $scope];
        $pns = new PlatformNotificationService($body);

        $this->assertEquals($scope, $pns->scope());
    }
}
