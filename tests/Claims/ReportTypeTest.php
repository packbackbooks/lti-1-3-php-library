<?php

namespace Tests\Claims;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\ReportType;
use Packback\Lti1p3\Messages\LtiMessage;
use Tests\TestCase;

class ReportTypeTest extends TestCase
{
    private $messageMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->messageMock = Mockery::mock(LtiMessage::class);
    }

    public function test_claim_key_returns_report_type_constant()
    {
        $this->assertEquals(Claim::ASSETREPORT_TYPE, ReportType::claimKey());
    }

    public function test_constructor_sets_body()
    {
        $body = 'grade';
        $reportType = new ReportType($body);

        $this->assertEquals($body, $reportType->getBody());
    }

    public function test_create_method_creates_instance_from_message()
    {
        $reportTypeData = 'completion';
        $messageBody = [Claim::ASSETREPORT_TYPE => $reportTypeData];
        $this->messageMock->shouldReceive('getBody')->andReturn($messageBody);

        $reportType = ReportType::create($this->messageMock);

        $this->assertInstanceOf(ReportType::class, $reportType);
        $this->assertEquals($reportTypeData, $reportType->getBody());
    }
}
