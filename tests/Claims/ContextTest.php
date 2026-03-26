<?php

namespace Tests\Claims;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\Context;
use Packback\Lti1p3\Messages\LtiMessage;
use Tests\TestCase;

class ContextTest extends TestCase
{
    private $messageMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->messageMock = Mockery::mock(LtiMessage::class);
    }

    public function test_claim_key_returns_context_constant()
    {
        $this->assertEquals(Claim::CONTEXT, Context::claimKey());
    }

    public function test_constructor_sets_body()
    {
        $body = ['id' => 'context-123', 'label' => 'CS101', 'title' => 'Computer Science 101'];
        $context = new Context($body);

        $this->assertEquals($body, $context->getBody());
    }

    public function test_create_method_creates_instance_from_message()
    {
        $contextData = ['id' => 'context-456', 'type' => ['Course']];
        $messageBody = [Claim::CONTEXT => $contextData];
        $this->messageMock->shouldReceive('getBody')->andReturn($messageBody);

        $context = Context::create($this->messageMock);

        $this->assertInstanceOf(Context::class, $context);
        $this->assertEquals($contextData, $context->getBody());
    }

    public function test_id_method_returns_id_from_body()
    {
        $contextId = 'context-789';
        $body = ['id' => $contextId, 'title' => 'Test Course'];
        $context = new Context($body);

        $this->assertEquals($contextId, $context->id());
    }

    public function test_label_method_returns_label_from_body()
    {
        $label = 'Biology 102';
        $body = ['id' => 'context-123', 'label' => $label, 'title' => 'Bio Adventures'];
        $context = new Context($body);

        $this->assertEquals($label, $context->label());
    }

    public function test_title_method_returns_title_from_body()
    {
        $title = 'Bio Adventures';
        $body = ['id' => 'context-123', 'label' => 'Biology 102', 'title' => $title];
        $context = new Context($body);

        $this->assertEquals($title, $context->title());
    }

    public function test_type_method_returns_type_from_body()
    {
        $type = ['http://purl.imsglobal.org/vocab/lis/v2/course#CourseSection'];
        $body = ['id' => 'context-123', 'type' => $type];
        $context = new Context($body);

        $this->assertEquals($type, $context->type());
    }
}
