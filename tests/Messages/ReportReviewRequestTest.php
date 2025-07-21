<?php

namespace Tests\Messages;

use Packback\Lti1p3\Messages\ReportReviewRequest;
use Tests\TestCase;

class ReportReviewRequestTest extends TestCase
{
    protected function setUp(): void {}

    public function test_it_creates_new_instance()
    {
        $reportReviewRequest = new ReportReviewRequest([]);

        $this->assertInstanceOf(ReportReviewRequest::class, $reportReviewRequest);
    }
}
