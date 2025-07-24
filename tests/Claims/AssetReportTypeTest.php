<?php

namespace Tests\Claims;

use Mockery;
use Packback\Lti1p3\Claims\AssetReportType;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Messages\LtiMessage;
use Tests\TestCase;

class AssetReportTypeTest extends TestCase
{
    private $messageMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->messageMock = Mockery::mock(LtiMessage::class);
    }

    public function test_claim_key_returns_asset_report_type_constant()
    {
        $this->assertEquals(Claim::ASSETREPORT_TYPE, AssetReportType::claimKey());
    }

    public function test_constructor_sets_body()
    {
        $body = ['type' => 'completion', 'format' => 'json'];
        $assetReportType = new AssetReportType($body);

        $this->assertEquals($body, $assetReportType->getBody());
    }

    public function test_create_method_creates_instance_from_message()
    {
        $assetReportTypeData = ['type' => 'grade', 'version' => '1.0'];
        $messageBody = [Claim::ASSETREPORT_TYPE => $assetReportTypeData];
        $this->messageMock->shouldReceive('getBody')->andReturn($messageBody);

        $assetReportType = AssetReportType::create($this->messageMock);

        $this->assertInstanceOf(AssetReportType::class, $assetReportType);
        $this->assertEquals($assetReportTypeData, $assetReportType->getBody());
    }
}
