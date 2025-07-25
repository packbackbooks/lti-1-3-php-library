<?php

namespace Tests\Claims;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\NamesRoleProvisioningService;
use Packback\Lti1p3\Messages\LtiMessage;
use Tests\TestCase;

class NamesRoleProvisioningServiceTest extends TestCase
{
    private $messageMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->messageMock = Mockery::mock(LtiMessage::class);
    }

    public function test_claim_key_returns_names_role_provisioning_service_constant()
    {
        $this->assertEquals(Claim::NRPS_NAMESROLESSERVICE, NamesRoleProvisioningService::claimKey());
    }

    public function test_constructor_sets_body()
    {
        $body = ['context_memberships_url' => 'https://example.com/memberships', 'service_versions' => ['2.0']];
        $nrps = new NamesRoleProvisioningService($body);

        $this->assertEquals($body, $nrps->getBody());
    }

    public function test_create_method_creates_instance_from_message()
    {
        $nrpsData = ['scope' => ['https://purl.imsglobal.org/spec/lti-nrps/scope/contextmembership.readonly']];
        $messageBody = [Claim::NRPS_NAMESROLESSERVICE => $nrpsData];
        $this->messageMock->shouldReceive('getBody')->andReturn($messageBody);

        $nrps = NamesRoleProvisioningService::create($this->messageMock);

        $this->assertInstanceOf(NamesRoleProvisioningService::class, $nrps);
        $this->assertEquals($nrpsData, $nrps->getBody());
    }

    public function test_context_memberships_url_method_returns_url_from_body()
    {
        $url = 'https://ltiadvantagevalidator.imsglobal.org/ltitool/namesandroles.html?memberships=1879';
        $body = ['context_memberships_url' => $url, 'service_versions' => ['2.0']];
        $nrps = new NamesRoleProvisioningService($body);

        $this->assertEquals($url, $nrps->contextMembershipsUrl());
    }

    public function test_service_versions_method_returns_versions_from_body()
    {
        $versions = ['2.0'];
        $body = ['context_memberships_url' => 'https://example.com/memberships', 'service_versions' => $versions];
        $nrps = new NamesRoleProvisioningService($body);

        $this->assertEquals($versions, $nrps->serviceVersions());
    }
}
