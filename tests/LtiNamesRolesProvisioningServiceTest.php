<?php

namespace Tests;

use Mockery;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\LtiServiceConnector;
use Packback\Lti1p3\LtiNamesRolesProvisioningService;
use PHPUnit\Framework\TestCase;

class LtiNamesRolesProvisioningServiceTest extends TestCase
{
    public function setUp(): void
    {
        $this->connector = Mockery::mock(ILtiServiceConnector::class);
    }

    public function testItInstantiates()
    {
        $nrps = new LtiNamesRolesProvisioningService($this->connector, []);

        $this->assertInstanceOf(LtiNamesRolesProvisioningService::class, $nrps);
    }

    public function testItGetsMembers()
    {
        $expected = [
            'headers' => [
                'Content-Type' => LtiServiceConnector::CONTENT_TYPE_JSON,
                'Server' => 'nginx',
            ],
            'body' => ['members'],
        ];

        $nrps = new LtiNamesRolesProvisioningService($this->connector, [
            'context_memberships_url' => 'url',
        ]);
        $this->connector->shouldReceive('getAll')
            ->once()->andReturn($expected);

        $result = $nrps->getMembers();

        $this->assertEquals($expected, $result);
    }
}
