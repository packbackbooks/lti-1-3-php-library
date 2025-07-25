<?php

namespace Tests\Messages;

use Mockery;
use Packback\Lti1p3\Claims\Activity;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\Context;
use Packback\Lti1p3\Claims\MessageType;
use Packback\Lti1p3\Claims\Roles;
use Packback\Lti1p3\Claims\TargetLinkUri;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\Messages\AssetProcessorSettingsRequest;
use Tests\TestCase;

class AssetProcessorSettingsRequestTest extends TestCase
{
    private $serviceConnectorMock;
    private $registrationMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serviceConnectorMock = Mockery::mock(ILtiServiceConnector::class);
        $this->registrationMock = Mockery::mock(ILtiRegistration::class);
    }

    public function test_message_type_returns_asset_processor_settings_constant()
    {
        $this->assertEquals('LtiAssetProcessorSettingsRequest', AssetProcessorSettingsRequest::messageType());
        $this->assertEquals(LtiConstants::MESSAGE_TYPE_ASSETPROCESSORSETTINGS, AssetProcessorSettingsRequest::messageType());
    }

    public function test_required_claims_returns_expected_claims()
    {
        $expectedClaims = [
            MessageType::claimKey(),
            TargetLinkUri::claimKey(),
            Roles::claimKey(),
            Activity::claimKey(),
            Context::claimKey(),
        ];

        $this->assertEquals($expectedClaims, AssetProcessorSettingsRequest::requiredClaims());
    }

    public function test_roles_claim_returns_roles_instance()
    {
        $roles = ['http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor'];
        $body = [Claim::ROLES => $roles];
        $message = new AssetProcessorSettingsRequest($this->serviceConnectorMock, $this->registrationMock, $body);

        $rolesClaim = $message->rolesClaim();

        $this->assertInstanceOf(Roles::class, $rolesClaim);
        $this->assertEquals($roles, $rolesClaim->getBody());
    }

    public function test_activity_claim_returns_activity_instance()
    {
        $activity = ['id' => 'activity-123', 'type' => 'assessment'];
        $body = [Claim::ACTIVITY => $activity];
        $message = new AssetProcessorSettingsRequest($this->serviceConnectorMock, $this->registrationMock, $body);

        $activityClaim = $message->activityClaim();

        $this->assertInstanceOf(Activity::class, $activityClaim);
        $this->assertEquals($activity, $activityClaim->getBody());
    }

    public function test_get_launch_id_returns_unique_string()
    {
        $message = new AssetProcessorSettingsRequest($this->serviceConnectorMock, $this->registrationMock, []);

        $launchId = $message->getLaunchId();

        $this->assertStringStartsWith('lti1p3_launch_', $launchId);
    }
}
