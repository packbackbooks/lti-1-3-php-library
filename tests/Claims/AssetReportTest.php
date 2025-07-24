<?php

namespace Tests\Claims;

use Mockery;
use Packback\Lti1p3\Claims\AssetReport;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Messages\LtiMessage;
use Tests\TestCase;

class AssetReportTest extends TestCase
{
    private $messageMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->messageMock = Mockery::mock(LtiMessage::class);
    }

    public function test_claim_key_returns_asset_report_constant()
    {
        $this->assertEquals(Claim::ASSETREPORT, AssetReport::claimKey());
    }

    public function test_constructor_sets_body()
    {
        $body = [
            'report_url' => 'https://example.com/report',
            'scope' => ['read', 'write'],
        ];
        $assetReport = new AssetReport($body);

        $this->assertEquals($body, $assetReport->getBody());
    }

    public function test_create_method_creates_instance_from_message()
    {
        $assetReportData = [
            'report_url' => 'https://example.com/asset-report',
            'scope' => ['view', 'edit'],
            'format' => 'json',
        ];
        $messageBody = [Claim::ASSETREPORT => $assetReportData];
        $this->messageMock->shouldReceive('getBody')->andReturn($messageBody);

        $assetReport = AssetReport::create($this->messageMock);

        $this->assertInstanceOf(AssetReport::class, $assetReport);
        $this->assertEquals($assetReportData, $assetReport->getBody());
    }

    public function test_report_url_method_returns_url_from_body()
    {
        $reportUrl = 'https://example.com/my-report';
        $body = [
            'report_url' => $reportUrl,
            'scope' => ['read'],
        ];
        $assetReport = new AssetReport($body);

        $this->assertEquals($reportUrl, $assetReport->reportUrl());
    }

    public function test_scope_method_returns_scope_from_body()
    {
        $scope = ['read', 'write', 'delete'];
        $body = [
            'report_url' => 'https://example.com/report',
            'scope' => $scope,
        ];
        $assetReport = new AssetReport($body);

        $this->assertEquals($scope, $assetReport->scope());
    }
}
