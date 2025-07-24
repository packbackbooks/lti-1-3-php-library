<?php

namespace Tests\Claims;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\DeploymentId;
use Packback\Lti1p3\Messages\LtiMessage;
use Tests\TestCase;

class DeploymentIdTest extends TestCase
{
    private $messageMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->messageMock = Mockery::mock(LtiMessage::class);
    }

    public function test_claim_key_returns_deployment_id_constant()
    {
        $this->assertEquals(Claim::DEPLOYMENT_ID, DeploymentId::claimKey());
    }

    public function test_constructor_sets_body()
    {
        $body = 'deployment-123';
        $deploymentId = new DeploymentId($body);

        $this->assertEquals($body, $deploymentId->getBody());
    }

    public function test_create_method_creates_instance_from_message()
    {
        $deploymentIdData = 'deployment-456';
        $messageBody = [Claim::DEPLOYMENT_ID => $deploymentIdData];
        $this->messageMock->shouldReceive('getBody')->andReturn($messageBody);

        $deploymentId = DeploymentId::create($this->messageMock);

        $this->assertInstanceOf(DeploymentId::class, $deploymentId);
        $this->assertEquals($deploymentIdData, $deploymentId->getBody());
    }
}
