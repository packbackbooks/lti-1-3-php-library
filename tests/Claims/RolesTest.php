<?php

namespace Tests\Claims;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\Roles;
use Packback\Lti1p3\Messages\LtiMessage;
use Tests\TestCase;

class RolesTest extends TestCase
{
    private $messageMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->messageMock = Mockery::mock(LtiMessage::class);
    }

    public function test_claim_key_returns_roles_constant()
    {
        $this->assertEquals(Claim::ROLES, Roles::claimKey());
    }

    public function test_constructor_sets_body()
    {
        $body = ['http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor'];
        $roles = new Roles($body);

        $this->assertEquals($body, $roles->getBody());
    }

    public function test_create_method_creates_instance_from_message()
    {
        $rolesData = ['http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'];
        $messageBody = [Claim::ROLES => $rolesData];
        $this->messageMock->shouldReceive('getBody')->andReturn($messageBody);

        $roles = Roles::create($this->messageMock);

        $this->assertInstanceOf(Roles::class, $roles);
        $this->assertEquals($rolesData, $roles->getBody());
    }
}
