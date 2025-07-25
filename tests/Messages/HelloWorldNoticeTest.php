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
        parent::setUp();
        $this->serviceConnectorMock = Mockery::mock(ILtiServiceConnector::class);
        $this->registrationMock = Mockery::mock(ILtiRegistration::class);
    }

    public function test_required_claims_returns_empty_array()
    {
        $expectedClaims = [];

        $this->assertEquals($expectedClaims, HelloWorldNotice::requiredClaims());
    }

    public function test_sub_method_returns_notice_type_from_body()
    {
        $body = ['sub' => 'LtiHelloWorldNotice'];
        $message = new HelloWorldNotice($this->serviceConnectorMock, $this->registrationMock, $body);

        $sub = $message->sub();

        $this->assertEquals('LtiHelloWorldNotice', $sub);
    }
}
