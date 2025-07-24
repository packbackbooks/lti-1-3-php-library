<?php

namespace Tests\Claims;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\ToolPlatform;
use Packback\Lti1p3\Messages\LtiMessage;
use Tests\TestCase;

class ToolPlatformTest extends TestCase
{
    private $messageMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->messageMock = Mockery::mock(LtiMessage::class);
    }

    public function test_claim_key_returns_tool_platform_constant()
    {
        $this->assertEquals(Claim::TOOL_PLATFORM, ToolPlatform::claimKey());
    }

    public function test_constructor_sets_body()
    {
        $body = ['guid' => 'platform-123', 'name' => 'Example LMS', 'version' => '1.0'];
        $toolPlatform = new ToolPlatform($body);

        $this->assertEquals($body, $toolPlatform->getBody());
    }

    public function test_create_method_creates_instance_from_message()
    {
        $toolPlatformData = ['contact_email' => 'admin@example.com', 'description' => 'Learning Management System'];
        $messageBody = [Claim::TOOL_PLATFORM => $toolPlatformData];
        $this->messageMock->shouldReceive('getBody')->andReturn($messageBody);

        $toolPlatform = ToolPlatform::create($this->messageMock);

        $this->assertInstanceOf(ToolPlatform::class, $toolPlatform);
        $this->assertEquals($toolPlatformData, $toolPlatform->getBody());
    }
}
