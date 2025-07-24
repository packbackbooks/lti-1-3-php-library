<?php

namespace Tests\Claims;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\Report;
use Packback\Lti1p3\Messages\LtiMessage;
use Tests\TestCase;

class ReportTest extends TestCase
{
    private $messageMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->messageMock = Mockery::mock(LtiMessage::class);
    }

    public function test_claim_key_returns_report_constant()
    {
        $this->assertEquals(Claim::ASSETREPORT, Report::claimKey());
    }

    public function test_constructor_sets_body()
    {
        $body = ['data' => ['score' => 85], 'format' => 'application/json'];
        $report = new Report($body);

        $this->assertEquals($body, $report->getBody());
    }

    public function test_create_method_creates_instance_from_message()
    {
        $reportData = ['results' => ['passed' => true], 'timestamp' => '2024-01-01T12:00:00Z'];
        $messageBody = [Claim::ASSETREPORT => $reportData];
        $this->messageMock->shouldReceive('getBody')->andReturn($messageBody);

        $report = Report::create($this->messageMock);

        $this->assertInstanceOf(Report::class, $report);
        $this->assertEquals($reportData, $report->getBody());
    }
}
