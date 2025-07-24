<?php

namespace Tests\Messages;

use Mockery;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\Messages\ReportReviewRequest;
use Tests\TestCase;

class ReportReviewRequestTest extends TestCase
{
    protected function setUp(): void
    {
        $this->serviceConnectorMock = Mockery::mock(ILtiServiceConnector::class);
        $this->registrationMock = Mockery::mock(ILtiRegistration::class);
    }
    private $serviceConnectorMock;
    private $registrationMock;

    public function test_it_creates_new_instance()
    {
        $reportReviewRequest = new ReportReviewRequest(
            $this->serviceConnectorMock,
            $this->registrationMock,
            []
        );

        $this->assertInstanceOf(ReportReviewRequest::class, $reportReviewRequest);
    }
}
