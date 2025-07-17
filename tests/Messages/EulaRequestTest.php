<?php

namespace Tests\Messages;

use Mockery;
use Packback\Lti1p3\Interfaces\ICache;
use Packback\Lti1p3\Interfaces\ICookie;
use Packback\Lti1p3\Interfaces\IDatabase;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\Messages\EulaRequest;
use Tests\TestCase;

class EulaRequestTest extends TestCase
{
    private EulaRequest $eulaRequest;
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
        $this->eulaRequest = new EulaRequest(
            $this->databaseMock,
            $this->cacheMock,
            $this->cookieMock,
            $this->serviceConnectorMock
        );

        $this->assertInstanceOf(EulaRequest::class, $this->eulaRequest);
    }
}
