<?php

namespace Tests\Claims;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\TargetLinkUri;
use Packback\Lti1p3\Messages\LtiMessage;
use Tests\TestCase;

class TargetLinkUriTest extends TestCase
{
    private $messageMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->messageMock = Mockery::mock(LtiMessage::class);
    }

    public function test_claim_key_returns_target_link_uri_constant()
    {
        $this->assertEquals(Claim::TARGET_LINK_URI, TargetLinkUri::claimKey());
    }

    public function test_constructor_sets_body()
    {
        $body = 'https://example.com/lti/launch';
        $targetLinkUri = new TargetLinkUri($body);

        $this->assertEquals($body, $targetLinkUri->getBody());
    }

    public function test_create_method_creates_instance_from_message()
    {
        $targetLinkUriData = 'https://tool.example.com/assignment/123';
        $messageBody = [Claim::TARGET_LINK_URI => $targetLinkUriData];
        $this->messageMock->shouldReceive('getBody')->andReturn($messageBody);

        $targetLinkUri = TargetLinkUri::create($this->messageMock);

        $this->assertInstanceOf(TargetLinkUri::class, $targetLinkUri);
        $this->assertEquals($targetLinkUriData, $targetLinkUri->getBody());
    }
}
