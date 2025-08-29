<?php

namespace Tests\Claims;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\Custom;
use Packback\Lti1p3\Messages\LtiMessage;
use Tests\TestCase;

class CustomTest extends TestCase
{
    private $messageMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->messageMock = Mockery::mock(LtiMessage::class);
    }

    public function test_claim_key_returns_custom_constant()
    {
        $this->assertEquals(Claim::CUSTOM, Custom::claimKey());
    }

    public function test_constructor_sets_body()
    {
        $body = ['param1' => 'value1', 'param2' => 'value2'];
        $custom = new Custom($body);

        $this->assertEquals($body, $custom->getBody());
    }

    public function test_create_method_creates_instance_from_message()
    {
        $customData = ['institution_id' => '12345', 'course_code' => 'CS101'];
        $messageBody = [Claim::CUSTOM => $customData];
        $this->messageMock->shouldReceive('getBody')->andReturn($messageBody);

        $custom = Custom::create($this->messageMock);

        $this->assertInstanceOf(Custom::class, $custom);
        $this->assertEquals($customData, $custom->getBody());
    }
}
