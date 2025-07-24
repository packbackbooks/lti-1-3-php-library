<?php

namespace Tests\Claims;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\DeepLinkSettings;
use Packback\Lti1p3\Messages\LtiMessage;
use Tests\TestCase;

class DeepLinkSettingsTest extends TestCase
{
    private $messageMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->messageMock = Mockery::mock(LtiMessage::class);
    }

    public function test_claim_key_returns_deep_link_settings_constant()
    {
        $this->assertEquals(Claim::DL_DEEP_LINK_SETTINGS, DeepLinkSettings::claimKey());
    }

    public function test_constructor_sets_body()
    {
        $body = ['deep_link_return_url' => 'https://example.com/return', 'accept_types' => ['link']];
        $deepLinkSettings = new DeepLinkSettings($body);

        $this->assertEquals($body, $deepLinkSettings->getBody());
    }

    public function test_create_method_creates_instance_from_message()
    {
        $deepLinkSettingsData = ['accept_presentation_document_targets' => ['iframe', 'window']];
        $messageBody = [Claim::DL_DEEP_LINK_SETTINGS => $deepLinkSettingsData];
        $this->messageMock->shouldReceive('getBody')->andReturn($messageBody);

        $deepLinkSettings = DeepLinkSettings::create($this->messageMock);

        $this->assertInstanceOf(DeepLinkSettings::class, $deepLinkSettings);
        $this->assertEquals($deepLinkSettingsData, $deepLinkSettings->getBody());
    }
}
