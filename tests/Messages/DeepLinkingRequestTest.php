<?php

namespace Tests\Messages;

use Mockery;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Messages\DeepLinkingRequest;
use Tests\TestCase;

class DeepLinkingRequestTest extends TestCase
{
    private $registrationMock;

    protected function setUp(): void
    {
        $this->registrationMock = Mockery::mock(ILtiRegistration::class);
    }

    public function test_it_creates_new_instance()
    {
        $deepLinkingRequest = new DeepLinkingRequest($this->registrationMock, []);

        $this->assertInstanceOf(DeepLinkingRequest::class, $deepLinkingRequest);
    }
}
