<?php

namespace Tests\Claims;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\ForUser;
use Packback\Lti1p3\Messages\LtiMessage;
use Tests\TestCase;

class ForUserTest extends TestCase
{
    private $messageMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->messageMock = Mockery::mock(LtiMessage::class);
    }

    public function test_claim_key_returns_for_user_constant()
    {
        $this->assertEquals(Claim::FOR_USER, ForUser::claimKey());
    }

    public function test_constructor_sets_body()
    {
        $body = [
            'user_id' => 'user-123',
            'person' => ['name' => 'John Doe', 'email' => 'john@example.com'],
        ];
        $forUser = new ForUser($body);

        $this->assertEquals($body, $forUser->getBody());
    }

    public function test_create_method_creates_instance_from_message()
    {
        $forUserData = [
            'user_id' => 'user-456',
            'roles' => ['Instructor'],
        ];
        $messageBody = [Claim::FOR_USER => $forUserData];
        $this->messageMock->shouldReceive('getBody')->andReturn($messageBody);

        $forUser = ForUser::create($this->messageMock);

        $this->assertInstanceOf(ForUser::class, $forUser);
        $this->assertEquals($forUserData, $forUser->getBody());
    }

    public function test_user_id_method_returns_user_id_from_body()
    {
        $userId = 'user-789';
        $body = ['user_id' => $userId, 'person' => ['name' => 'Jane Doe']];
        $forUser = new ForUser($body);

        $this->assertEquals($userId, $forUser->userId());
    }
}
