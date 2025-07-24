<?php

namespace Tests\Messages;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\MessageType;
use Packback\Lti1p3\Claims\ResourceLink;
use Packback\Lti1p3\Claims\TargetLinkUri;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\Messages\ResourceLinkRequest;
use Tests\TestCase;

class ResourceLinkRequestTest extends TestCase
{
    private $serviceConnectorMock;
    private $registrationMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serviceConnectorMock = Mockery::mock(ILtiServiceConnector::class);
        $this->registrationMock = Mockery::mock(ILtiRegistration::class);
    }

    public function test_message_type_returns_resource_constant()
    {
        $this->assertEquals('LtiResourceLinkRequest', ResourceLinkRequest::messageType());
        $this->assertEquals(LtiConstants::MESSAGE_TYPE_RESOURCE, ResourceLinkRequest::messageType());
    }

    public function test_required_claims_returns_expected_claims()
    {
        $expectedClaims = [
            MessageType::claimKey(),
            TargetLinkUri::claimKey(),
            ResourceLink::claimKey(),
        ];

        $this->assertEquals($expectedClaims, ResourceLinkRequest::requiredClaims());
    }

    public function test_resource_link_claim_returns_resource_link_instance()
    {
        $resourceLink = ['id' => 'resource-123', 'title' => 'Test Resource'];
        $body = [Claim::RESOURCE_LINK => $resourceLink];
        $message = new ResourceLinkRequest($this->serviceConnectorMock, $this->registrationMock, $body);

        $resourceLinkClaim = $message->resourceLinkClaim();

        $this->assertInstanceOf(ResourceLink::class, $resourceLinkClaim);
        $this->assertEquals($resourceLink, $resourceLinkClaim->getBody());
    }

    public function test_get_launch_id_returns_unique_string()
    {
        $message = new ResourceLinkRequest($this->serviceConnectorMock, $this->registrationMock, []);

        $launchId = $message->getLaunchId();

        $this->assertStringStartsWith('lti1p3_launch_', $launchId);
    }
}
