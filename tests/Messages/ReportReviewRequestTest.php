<?php

namespace Tests\Messages;

use Mockery;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Messages\ReportReviewRequest;
use Tests\TestCase;

class ReportReviewRequestTest extends TestCase
{
    private $registrationMock;

    protected function setUp(): void
    {
        $this->registrationMock = Mockery::mock(ILtiRegistration::class);
    }

    public function test_it_creates_new_instance()
    {
        $reportReviewRequest = new ReportReviewRequest($this->registrationMock, []);

        $this->assertInstanceOf(ReportReviewRequest::class, $reportReviewRequest);
    }
}
