<?php

namespace Tests\Claims;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\GroupService;
use Packback\Lti1p3\Messages\LtiMessage;
use Tests\TestCase;

class GroupServiceTest extends TestCase
{
    private $messageMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->messageMock = Mockery::mock(LtiMessage::class);
    }

    public function test_claim_key_returns_group_service_constant()
    {
        $this->assertEquals(Claim::GS_GROUPSSERVICE, GroupService::claimKey());
    }

    public function test_constructor_sets_body()
    {
        $body = ['context_groups_url' => 'https://example.com/groups', 'service_version' => '2.0'];
        $groupService = new GroupService($body);

        $this->assertEquals($body, $groupService->getBody());
    }

    public function test_create_method_creates_instance_from_message()
    {
        $groupServiceData = ['scope' => ['https://purl.imsglobal.org/spec/lti-gs/scope/contextgroup.readonly']];
        $messageBody = [Claim::GS_GROUPSSERVICE => $groupServiceData];
        $this->messageMock->shouldReceive('getBody')->andReturn($messageBody);

        $groupService = GroupService::create($this->messageMock);

        $this->assertInstanceOf(GroupService::class, $groupService);
        $this->assertEquals($groupServiceData, $groupService->getBody());
    }
}
