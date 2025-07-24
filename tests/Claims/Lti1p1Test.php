<?php

namespace Tests\Claims;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\Lti1p1;
use Packback\Lti1p3\Messages\LtiMessage;
use Tests\TestCase;

class Lti1p1Test extends TestCase
{
    private $messageMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->messageMock = Mockery::mock(LtiMessage::class);
    }

    public function test_claim_key_returns_lti1p1_constant()
    {
        $this->assertEquals(Claim::LTI1P1, Lti1p1::claimKey());
    }

    public function test_constructor_sets_body()
    {
        $body = [
            'oauth_consumer_key_sign' => 'consumer-key-123',
            'user_id' => 'user-456',
            'roles' => 'Instructor',
        ];
        $lti1p1 = new Lti1p1($body);

        $this->assertEquals($body, $lti1p1->getBody());
    }

    public function test_create_method_creates_instance_from_message()
    {
        $lti1p1Data = [
            'oauth_consumer_key_sign' => 'consumer-key-789',
            'context_id' => 'context-123',
        ];
        $messageBody = [Claim::LTI1P1 => $lti1p1Data];
        $this->messageMock->shouldReceive('getBody')->andReturn($messageBody);

        $lti1p1 = Lti1p1::create($this->messageMock);

        $this->assertInstanceOf(Lti1p1::class, $lti1p1);
        $this->assertEquals($lti1p1Data, $lti1p1->getBody());
    }

    public function test_get_oauth_consumer_key_sign_method_returns_key_from_body()
    {
        $oauthKey = 'oauth-consumer-key-sign-123';
        $body = [
            'oauth_consumer_key_sign' => $oauthKey,
            'user_id' => 'user-456',
        ];
        $lti1p1 = new Lti1p1($body);

        $this->assertEquals($oauthKey, $lti1p1->getOauthConsumerKeySign());
    }
}
