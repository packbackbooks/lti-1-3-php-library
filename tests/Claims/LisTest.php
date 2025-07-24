<?php

namespace Tests\Claims;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\Lis;
use Packback\Lti1p3\Messages\LtiMessage;
use Tests\TestCase;

class LisTest extends TestCase
{
    private $messageMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->messageMock = Mockery::mock(LtiMessage::class);
    }

    public function test_claim_key_returns_lis_constant()
    {
        $this->assertEquals(Claim::LIS, Lis::claimKey());
    }

    public function test_constructor_sets_body()
    {
        $body = ['person_sourcedid' => '12345', 'course_offering_sourcedid' => 'CS101-2024'];
        $lis = new Lis($body);

        $this->assertEquals($body, $lis->getBody());
    }

    public function test_create_method_creates_instance_from_message()
    {
        $lisData = ['outcome_service_url' => 'https://example.com/outcomes'];
        $messageBody = [Claim::LIS => $lisData];
        $this->messageMock->shouldReceive('getBody')->andReturn($messageBody);

        $lis = Lis::create($this->messageMock);

        $this->assertInstanceOf(Lis::class, $lis);
        $this->assertEquals($lisData, $lis->getBody());
    }
}
