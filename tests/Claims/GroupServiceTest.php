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

    public function test_scope_method_returns_scope_from_body()
    {
        $scope = ['https://purl.imsglobal.org/spec/lti-gs/scope/contextgroup.readonly'];
        $body = ['scope' => $scope, 'context_groups_url' => 'https://example.com/groups'];
        $groupService = new GroupService($body);

        $this->assertEquals($scope, $groupService->scope());
    }

    public function test_context_groups_url_method_returns_url_from_body()
    {
        $contextGroupsUrl = 'https://www.myuniv.example.com/2344/groups';
        $body = ['context_groups_url' => $contextGroupsUrl, 'service_versions' => ['1.0']];
        $groupService = new GroupService($body);

        $this->assertEquals($contextGroupsUrl, $groupService->contextGroupsUrl());
    }

    public function test_context_group_sets_url_method_returns_url_from_body()
    {
        $contextGroupSetsUrl = 'https://www.myuniv.example.com/2344/groups/sets';
        $body = ['context_group_sets_url' => $contextGroupSetsUrl];
        $groupService = new GroupService($body);

        $this->assertEquals($contextGroupSetsUrl, $groupService->contextGroupSetsUrl());
    }

    public function test_service_versions_method_returns_versions_from_body()
    {
        $serviceVersions = ['1.0'];
        $body = ['context_groups_url' => 'https://example.com/groups', 'service_versions' => $serviceVersions];
        $groupService = new GroupService($body);

        $this->assertEquals($serviceVersions, $groupService->serviceVersions());
    }
}
