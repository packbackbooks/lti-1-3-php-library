<?php

namespace Tests\Claims;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\Data;
use Packback\Lti1p3\Messages\LtiMessage;
use Tests\TestCase;

class DataTest extends TestCase
{
    private $messageMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->messageMock = Mockery::mock(LtiMessage::class);
    }

    public function test_claim_key_returns_data_constant()
    {
        $this->assertEquals(Claim::DL_DATA, Data::claimKey());
    }

    public function test_constructor_sets_body()
    {
        $body = ['return_url' => 'https://example.com/return', 'extra' => 'info'];
        $data = new Data($body);

        $this->assertEquals($body, $data->getBody());
    }

    public function test_create_method_creates_instance_from_message()
    {
        $dataData = ['session_id' => 'abc123', 'user_context' => 'teacher'];
        $messageBody = [Claim::DL_DATA => $dataData];
        $this->messageMock->shouldReceive('getBody')->andReturn($messageBody);

        $data = Data::create($this->messageMock);

        $this->assertInstanceOf(Data::class, $data);
        $this->assertEquals($dataData, $data->getBody());
    }
}
