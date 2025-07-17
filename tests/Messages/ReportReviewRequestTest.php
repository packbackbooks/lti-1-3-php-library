<?php

namespace Tests\Messages;

use Mockery;
use Packback\Lti1p3\Interfaces\ICache;
use Packback\Lti1p3\Interfaces\ICookie;
use Packback\Lti1p3\Interfaces\IDatabase;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\Messages\ReportReviewRequest;
use Tests\TestCase;

class ReportReviewRequestTest extends TestCase
{
    private ReportReviewRequest $reportReviewRequest;
    private $databaseMock;
    private $cacheMock;
    private $cookieMock;
    private $serviceConnectorMock;

    protected function setUp(): void
    {
        $this->databaseMock = Mockery::mock(IDatabase::class);
        $this->cacheMock = Mockery::mock(ICache::class);
        $this->cookieMock = Mockery::mock(ICookie::class);
        $this->serviceConnectorMock = Mockery::mock(ILtiServiceConnector::class);
    }

    public function test_it_creates_new_instance()
    {
        $this->reportReviewRequest = new ReportReviewRequest(
            $this->databaseMock,
            $this->cacheMock,
            $this->cookieMock,
            $this->serviceConnectorMock
        );

        $this->assertInstanceOf(ReportReviewRequest::class, $this->reportReviewRequest);
    }
}
