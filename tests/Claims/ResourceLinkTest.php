<?php

namespace Tests\Claims;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\ResourceLink;
use Packback\Lti1p3\Messages\LtiMessage;
use Tests\TestCase;

class ResourceLinkTest extends TestCase
{
    private $messageMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->messageMock = Mockery::mock(LtiMessage::class);
    }

    public function test_claim_key_returns_resource_link_constant()
    {
        $this->assertEquals(Claim::RESOURCE_LINK, ResourceLink::claimKey());
    }

    public function test_constructor_sets_body()
    {
        $body = ['id' => 'resource-123', 'title' => 'Test Assignment'];
        $resourceLink = new ResourceLink($body);

        $this->assertEquals($body, $resourceLink->getBody());
    }

    public function test_create_method_creates_instance_from_message()
    {
        $resourceLinkData = ['id' => 'resource-456', 'description' => 'Quiz on Chapter 1'];
        $messageBody = [Claim::RESOURCE_LINK => $resourceLinkData];
        $this->messageMock->shouldReceive('getBody')->andReturn($messageBody);

        $resourceLink = ResourceLink::create($this->messageMock);

        $this->assertInstanceOf(ResourceLink::class, $resourceLink);
        $this->assertEquals($resourceLinkData, $resourceLink->getBody());
    }

    public function test_id_method_returns_id_from_body()
    {
        $resourceId = 'resource-789';
        $body = ['id' => $resourceId, 'title' => 'Test Assignment'];
        $resourceLink = new ResourceLink($body);

        $this->assertEquals($resourceId, $resourceLink->id());
    }

    public function test_title_method_returns_title_from_body()
    {
        $title = 'Introduction Assignment';
        $body = ['id' => 'resource-123', 'title' => $title];
        $resourceLink = new ResourceLink($body);

        $this->assertEquals($title, $resourceLink->title());
    }

    public function test_description_method_returns_description_from_body()
    {
        $description = 'This is the introduction assignment';
        $body = ['id' => 'resource-123', 'description' => $description];
        $resourceLink = new ResourceLink($body);

        $this->assertEquals($description, $resourceLink->description());
    }
}
