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

    public function test_errors_method_returns_errors_from_body()
    {
        $errors = ['errors' => ['validation_error' => 'Invalid platform']];
        $body = ['guid' => 'platform-123', 'errors' => $errors];
        $toolPlatform = new ToolPlatform($body);

        $this->assertEquals($errors, $toolPlatform->errors());
    }

    public function test_guid_method_returns_guid_from_body()
    {
        $guid = 'KnQbfmlzZWjswfYmnKN7QKTohFOeRn8Jtm6R5GGw:canvas-lms';
        $body = ['guid' => $guid, 'name' => 'Packback Engineering'];
        $toolPlatform = new ToolPlatform($body);

        $this->assertEquals($guid, $toolPlatform->guid());
    }

    public function test_name_method_returns_name_from_body()
    {
        $name = 'Packback Engineering';
        $body = ['name' => $name, 'version' => 'cloud'];
        $toolPlatform = new ToolPlatform($body);

        $this->assertEquals($name, $toolPlatform->name());
    }

    public function test_version_method_returns_version_from_body()
    {
        $version = 'cloud';
        $body = ['version' => $version, 'product_family_code' => 'canvas'];
        $toolPlatform = new ToolPlatform($body);

        $this->assertEquals($version, $toolPlatform->version());
    }

    public function test_product_family_code_method_returns_code_from_body()
    {
        $productFamilyCode = 'canvas';
        $body = ['product_family_code' => $productFamilyCode, 'guid' => 'platform-123'];
        $toolPlatform = new ToolPlatform($body);

        $this->assertEquals($productFamilyCode, $toolPlatform->productFamilyCode());
    }

    public function test_validation_context_method_returns_validation_context_from_body()
    {
        $validationContext = ['context' => 'platform_validation'];
        $body = ['validation_context' => $validationContext];
        $toolPlatform = new ToolPlatform($body);

        $this->assertEquals($validationContext, $toolPlatform->validationContext());
    }
}
