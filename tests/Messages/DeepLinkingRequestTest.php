<?php

namespace Tests\Messages;

use Mockery;
use Packback\Lti1p3\Claims\Activity;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\DeepLinkSettings;
use Packback\Lti1p3\Claims\MessageType;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\LtiDeepLink;
use Packback\Lti1p3\Messages\DeepLinkingRequest;
use Tests\TestCase;

class DeepLinkingRequestTest extends TestCase
{
    private $serviceConnectorMock;
    private $registrationMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serviceConnectorMock = Mockery::mock(ILtiServiceConnector::class);
        $this->registrationMock = Mockery::mock(ILtiRegistration::class);
    }

    public function test_message_type_returns_deeplink_constant()
    {
        $this->assertEquals('LtiDeepLinkingRequest', DeepLinkingRequest::messageType());
        $this->assertEquals(LtiConstants::MESSAGE_TYPE_DEEPLINK, DeepLinkingRequest::messageType());
    }

    public function test_required_claims_returns_expected_claims()
    {
        $expectedClaims = [
            MessageType::claimKey(),
            DeepLinkSettings::claimKey(),
        ];

        $this->assertEquals($expectedClaims, DeepLinkingRequest::requiredClaims());
    }

    public function test_deep_link_settings_claim_returns_deep_link_settings_instance()
    {
        $deepLinkSettings = [
            'deep_link_return_url' => 'https://example.com/return',
            'accept_types' => ['ltiResourceLink'],
        ];
        $body = [Claim::DL_DEEP_LINK_SETTINGS => $deepLinkSettings];
        $message = new DeepLinkingRequest($this->serviceConnectorMock, $this->registrationMock, $body);

        $deepLinkSettingsClaim = $message->deepLinkSettingsClaim();

        $this->assertInstanceOf(DeepLinkSettings::class, $deepLinkSettingsClaim);
        $this->assertEquals($deepLinkSettings, $deepLinkSettingsClaim->getBody());
    }

    public function test_activity_claim_returns_activity_instance()
    {
        $activity = ['id' => 'activity-789', 'type' => 'assignment'];
        $body = [Claim::ACTIVITY => $activity];
        $message = new DeepLinkingRequest($this->serviceConnectorMock, $this->registrationMock, $body);

        $activityClaim = $message->activityClaim();

        $this->assertInstanceOf(Activity::class, $activityClaim);
        $this->assertEquals($activity, $activityClaim->getBody());
    }

    public function test_get_deep_link_returns_lti_deep_link_instance()
    {
        $deepLinkSettings = ['deep_link_return_url' => 'https://example.com/return'];
        $deploymentId = 'deployment-123';
        $body = [
            Claim::DL_DEEP_LINK_SETTINGS => $deepLinkSettings,
            Claim::DEPLOYMENT_ID => $deploymentId,
        ];
        $message = new DeepLinkingRequest($this->serviceConnectorMock, $this->registrationMock, $body);

        $deepLink = $message->getDeepLink();

        $this->assertInstanceOf(LtiDeepLink::class, $deepLink);
    }

    public function test_get_launch_id_returns_unique_string()
    {
        $message = new DeepLinkingRequest($this->serviceConnectorMock, $this->registrationMock, []);

        $launchId = $message->getLaunchId();

        $this->assertIsString($launchId);
        $this->assertStringStartsWith('lti1p3_launch_', $launchId);
    }
}
