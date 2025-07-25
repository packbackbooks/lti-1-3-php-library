<?php

namespace Tests\Claims;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Messages\LtiMessage;
use Tests\TestCase;

class ClaimTest extends TestCase
{
    private $messageMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->messageMock = Mockery::mock(LtiMessage::class);
    }

    public function test_get_body_returns_body()
    {
        $body = ['key' => 'value', 'another' => 'data'];
        $claim = new class($body) extends Claim
        {
            public static function claimKey(): string
            {
                return 'test_claim';
            }
        };

        $this->assertEquals($body, $claim->getBody());
    }

    public function test_create_method_creates_instance_from_message()
    {
        $messageBody = ['test_claim' => ['data' => 'value']];
        $this->messageMock->shouldReceive('getBody')->andReturn($messageBody);

        $claimClass = new class(['data' => 'value']) extends Claim
        {
            public static function claimKey(): string
            {
                return 'test_claim';
            }
        };

        $claim = $claimClass::create($this->messageMock);

        $this->assertInstanceOf(get_class($claimClass), $claim);
        $this->assertEquals(['data' => 'value'], $claim->getBody());
    }
}
