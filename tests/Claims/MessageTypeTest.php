<?php

namespace Tests\Claims;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\MessageType;
use Packback\Lti1p3\Messages\LtiMessage;
use Tests\TestCase;

class MessageTypeTest extends TestCase
{
    private $messageMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->messageMock = Mockery::mock(LtiMessage::class);
    }

    public function test_claim_key_returns_message_type_constant()
    {
        $this->assertEquals(Claim::MESSAGE_TYPE, MessageType::claimKey());
    }

    public function test_constructor_sets_body()
    {
        $body = 'LtiResourceLinkRequest';
        $messageType = new MessageType($body);

        $this->assertEquals($body, $messageType->getBody());
    }

    public function test_create_method_creates_instance_from_message()
    {
        $messageTypeData = 'LtiDeepLinkingRequest';
        $messageBody = [Claim::MESSAGE_TYPE => $messageTypeData];
        $this->messageMock->shouldReceive('getBody')->andReturn($messageBody);

        $messageType = MessageType::create($this->messageMock);

        $this->assertInstanceOf(MessageType::class, $messageType);
        $this->assertEquals($messageTypeData, $messageType->getBody());
    }
}
