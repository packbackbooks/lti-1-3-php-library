<?php

namespace Tests\Claims;

use Mockery;
use Packback\Lti1p3\Claims\Activity;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Messages\LtiMessage;
use Tests\TestCase;

class ActivityTest extends TestCase
{
    private $messageMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->messageMock = Mockery::mock(LtiMessage::class);
    }

    public function test_claim_key_returns_activity_constant()
    {
        $this->assertEquals(Claim::ACTIVITY, Activity::claimKey());
    }

    public function test_constructor_sets_body()
    {
        $body = ['id' => 'activity-123', 'type' => 'assessment'];
        $activity = new Activity($body);

        $this->assertEquals($body, $activity->getBody());
    }

    public function test_create_method_creates_instance_from_message()
    {
        $activityData = [
            'id' => 'activity-456',
            'type' => 'assignment',
            'properties' => ['duration' => 60],
        ];
        $messageBody = [Claim::ACTIVITY => $activityData];
        $this->messageMock->shouldReceive('getBody')->andReturn($messageBody);

        $activity = Activity::create($this->messageMock);

        $this->assertInstanceOf(Activity::class, $activity);
        $this->assertEquals($activityData, $activity->getBody());
    }

    public function test_id_method_returns_id_from_body()
    {
        $activityId = 'activity-789';
        $body = ['id' => $activityId, 'name' => 'Test Activity'];
        $activity = new Activity($body);

        $this->assertEquals($activityId, $activity->id());
    }
}
