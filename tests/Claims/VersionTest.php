<?php

namespace Tests\Claims;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\Version;
use Packback\Lti1p3\Messages\LtiMessage;
use Tests\TestCase;

class VersionTest extends TestCase
{
    private $messageMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->messageMock = Mockery::mock(LtiMessage::class);
    }

    public function test_claim_key_returns_version_constant()
    {
        $this->assertEquals(Claim::VERSION, Version::claimKey());
    }

    public function test_constructor_sets_body()
    {
        $body = '1.3.0';
        $version = new Version($body);

        $this->assertEquals($body, $version->getBody());
    }

    public function test_create_method_creates_instance_from_message()
    {
        $versionData = '1.3.0';
        $messageBody = [Claim::VERSION => $versionData];
        $this->messageMock->shouldReceive('getBody')->andReturn($messageBody);

        $version = Version::create($this->messageMock);

        $this->assertInstanceOf(Version::class, $version);
        $this->assertEquals($versionData, $version->getBody());
    }
}
