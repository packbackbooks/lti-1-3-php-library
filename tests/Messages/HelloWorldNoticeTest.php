<?php

namespace Tests\Messages;

use Mockery;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\Messages\HelloWorldNotice;
use Tests\TestCase;

class HelloWorldNoticeTest extends TestCase
{
    private $serviceConnectorMock;
    private $registrationMock;

    protected function setUp(): void
    {
        $this->serviceConnectorMock = Mockery::mock(ILtiServiceConnector::class);
        $this->registrationMock = Mockery::mock(ILtiRegistration::class);
    }

    public function test_it_creates_new_instance()
    {
        $helloWorldNotice = new HelloWorldNotice(
            $this->serviceConnectorMock,
            $this->registrationMock,
            []
        );
        $this->assertInstanceOf(HelloWorldNotice::class, $helloWorldNotice);
    }
}
