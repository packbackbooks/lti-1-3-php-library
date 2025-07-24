<?php

namespace Tests\Messages;

use Mockery;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\Messages\DeepLinkingRequest;
use Tests\TestCase;

class DeepLinkingRequestTest extends TestCase
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
        $deepLinkingRequest = new DeepLinkingRequest(
            $this->serviceConnectorMock,
            $this->registrationMock,
            []
        );

        $this->assertInstanceOf(DeepLinkingRequest::class, $deepLinkingRequest);
    }
}
