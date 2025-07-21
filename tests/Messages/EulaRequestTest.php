<?php

namespace Tests\Messages;

use Mockery;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Messages\EulaRequest;
use Tests\TestCase;

class EulaRequestTest extends TestCase
{
    private $registrationMock;

    protected function setUp(): void
    {
        $this->registrationMock = Mockery::mock(ILtiRegistration::class);
    }

    public function test_it_creates_new_instance()
    {
        $eulaRequest = new EulaRequest($this->registrationMock, []);

        $this->assertInstanceOf(EulaRequest::class, $eulaRequest);
    }
}
