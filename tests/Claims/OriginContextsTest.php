<?php

namespace Tests\Claims;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\OriginContexts;
use Packback\Lti1p3\Messages\LtiMessage;
use Tests\TestCase;

class OriginContextsTest extends TestCase
{
    private $messageMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->messageMock = Mockery::mock(LtiMessage::class);
    }

    public function test_claim_key_returns_origin_contexts_constant()
    {
        $this->assertEquals(Claim::ORIGIN_CONTEXTS, OriginContexts::claimKey());
    }

    public function test_constructor_sets_body()
    {
        $body = [['id' => 'context-1'], ['id' => 'context-2']];
        $originContexts = new OriginContexts($body);

        $this->assertEquals($body, $originContexts->getBody());
    }

    public function test_create_method_creates_instance_from_message()
    {
        $originContextsData = [['id' => 'course-123', 'type' => 'Course']];
        $messageBody = [Claim::ORIGIN_CONTEXTS => $originContextsData];
        $this->messageMock->shouldReceive('getBody')->andReturn($messageBody);

        $originContexts = OriginContexts::create($this->messageMock);

        $this->assertInstanceOf(OriginContexts::class, $originContexts);
        $this->assertEquals($originContextsData, $originContexts->getBody());
    }
}
