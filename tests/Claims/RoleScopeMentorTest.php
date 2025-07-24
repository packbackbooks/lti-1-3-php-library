<?php

namespace Tests\Claims;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\RoleScopeMentor;
use Packback\Lti1p3\Messages\LtiMessage;
use Tests\TestCase;

class RoleScopeMentorTest extends TestCase
{
    private $messageMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->messageMock = Mockery::mock(LtiMessage::class);
    }

    public function test_claim_key_returns_role_scope_mentor_constant()
    {
        $this->assertEquals(Claim::ROLE_SCOPE_MENTOR, RoleScopeMentor::claimKey());
    }

    public function test_constructor_sets_body()
    {
        $body = ['user-123', 'user-456'];
        $roleScopeMentor = new RoleScopeMentor($body);

        $this->assertEquals($body, $roleScopeMentor->getBody());
    }

    public function test_create_method_creates_instance_from_message()
    {
        $roleScopeMentorData = ['mentor-789'];
        $messageBody = [Claim::ROLE_SCOPE_MENTOR => $roleScopeMentorData];
        $this->messageMock->shouldReceive('getBody')->andReturn($messageBody);

        $roleScopeMentor = RoleScopeMentor::create($this->messageMock);

        $this->assertInstanceOf(RoleScopeMentor::class, $roleScopeMentor);
        $this->assertEquals($roleScopeMentorData, $roleScopeMentor->getBody());
    }
}
