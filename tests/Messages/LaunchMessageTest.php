<?php

namespace Tests\Messages;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\Context;
use Packback\Lti1p3\Claims\Custom;
use Packback\Lti1p3\Claims\LaunchPresentation;
use Packback\Lti1p3\Claims\Lis;
use Packback\Lti1p3\Claims\Lti1p1;
use Packback\Lti1p3\Claims\MessageType;
use Packback\Lti1p3\Claims\PlatformNotificationService;
use Packback\Lti1p3\Claims\RoleScopeMentor;
use Packback\Lti1p3\Claims\TargetLinkUri;
use Packback\Lti1p3\Claims\ToolPlatform;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\Messages\LaunchMessage;
use Tests\TestCase;

class LaunchMessageTest extends TestCase
{
    private $serviceConnectorMock;
    private $registrationMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serviceConnectorMock = Mockery::mock(ILtiServiceConnector::class);
        $this->registrationMock = Mockery::mock(ILtiRegistration::class);
    }

    public function test_get_launch_id_returns_unique_string()
    {
        $message1 = $this->createTestLaunchMessage();
        $message2 = $this->createTestLaunchMessage();

        $launchId1 = $message1->getLaunchId();
        $launchId2 = $message2->getLaunchId();

        $this->assertNotEquals($launchId1, $launchId2);
        $this->assertStringStartsWith('lti1p3_launch_', $launchId1);
    }

    public function test_message_type_claim_returns_message_type_instance()
    {
        $messageType = 'LtiResourceLinkRequest';
        $body = [Claim::MESSAGE_TYPE => $messageType];
        $message = $this->createTestLaunchMessage($body);

        $messageTypeClaim = $message->messageTypeClaim();

        $this->assertInstanceOf(MessageType::class, $messageTypeClaim);
        $this->assertEquals($messageType, $messageTypeClaim->getBody());
    }

    public function test_target_link_uri_claim_returns_target_link_uri_instance()
    {
        $targetLinkUri = 'https://example.com/launch';
        $body = [Claim::TARGET_LINK_URI => $targetLinkUri];
        $message = $this->createTestLaunchMessage($body);

        $targetLinkUriClaim = $message->targetLinkUriClaim();

        $this->assertInstanceOf(TargetLinkUri::class, $targetLinkUriClaim);
        $this->assertEquals($targetLinkUri, $targetLinkUriClaim->getBody());
    }

    public function test_tool_platform_claim_returns_tool_platform_instance()
    {
        $toolPlatform = ['guid' => 'platform-123', 'name' => 'Test Platform'];
        $body = [Claim::TOOL_PLATFORM => $toolPlatform];
        $message = $this->createTestLaunchMessage($body);

        $toolPlatformClaim = $message->toolPlatformClaim();

        $this->assertInstanceOf(ToolPlatform::class, $toolPlatformClaim);
        $this->assertEquals($toolPlatform, $toolPlatformClaim->getBody());
    }

    public function test_role_scope_mentor_claim_returns_role_scope_mentor_instance()
    {
        $roleScopeMentor = ['user-123', 'user-456'];
        $body = [Claim::ROLE_SCOPE_MENTOR => $roleScopeMentor];
        $message = $this->createTestLaunchMessage($body);

        $roleScopeMentorClaim = $message->roleScopeMentorClaim();

        $this->assertInstanceOf(RoleScopeMentor::class, $roleScopeMentorClaim);
        $this->assertEquals($roleScopeMentor, $roleScopeMentorClaim->getBody());
    }

    public function test_launch_presentation_claim_returns_launch_presentation_instance()
    {
        $launchPresentation = ['document_target' => 'iframe', 'return_url' => 'https://example.com/return'];
        $body = [Claim::LAUNCH_PRESENTATION => $launchPresentation];
        $message = $this->createTestLaunchMessage($body);

        $launchPresentationClaim = $message->launchPresentationClaim();

        $this->assertInstanceOf(LaunchPresentation::class, $launchPresentationClaim);
        $this->assertEquals($launchPresentation, $launchPresentationClaim->getBody());
    }

    public function test_lis_claim_returns_lis_instance()
    {
        $lis = ['person_sourcedid' => '12345', 'course_offering_sourcedid' => 'CS101'];
        $body = [Claim::LIS => $lis];
        $message = $this->createTestLaunchMessage($body);

        $lisClaim = $message->lisClaim();

        $this->assertInstanceOf(Lis::class, $lisClaim);
        $this->assertEquals($lis, $lisClaim->getBody());
    }

    public function test_custom_claim_returns_custom_instance()
    {
        $custom = ['param1' => 'value1', 'param2' => 'value2'];
        $body = [Claim::CUSTOM => $custom];
        $message = $this->createTestLaunchMessage($body);

        $customClaim = $message->customClaim();

        $this->assertInstanceOf(Custom::class, $customClaim);
        $this->assertEquals($custom, $customClaim->getBody());
    }

    public function test_lti_claim_1p1_returns_lti1p1_instance()
    {
        $lti1p1 = ['oauth_consumer_key_sign' => 'consumer-key-123', 'user_id' => 'user-456'];
        $body = [Claim::LTI1P1 => $lti1p1];
        $message = $this->createTestLaunchMessage($body);

        $lti1p1Claim = $message->ltiClaim1p1();

        $this->assertInstanceOf(Lti1p1::class, $lti1p1Claim);
        $this->assertEquals($lti1p1, $lti1p1Claim->getBody());
    }

    public function test_platform_notification_service_claim_returns_platform_notification_service_instance()
    {
        $pns = ['platform_notification_service_url' => 'https://example.com/notifications'];
        $body = [Claim::PLATFORMNOTIFICATIONSERVICE => $pns];
        $message = $this->createTestLaunchMessage($body);

        $pnsClaim = $message->platformNotificationServiceClaim();

        $this->assertInstanceOf(PlatformNotificationService::class, $pnsClaim);
        $this->assertEquals($pns, $pnsClaim->getBody());
    }

    public function test_context_claim_returns_context_instance()
    {
        $context = ['id' => 'context-123', 'label' => 'CS101', 'title' => 'Computer Science 101'];
        $body = [Claim::CONTEXT => $context];
        $message = $this->createTestLaunchMessage($body);

        $contextClaim = $message->contextClaim();

        $this->assertInstanceOf(Context::class, $contextClaim);
        $this->assertEquals($context, $contextClaim->getBody());
    }

    private function createTestLaunchMessage(array $body = []): LaunchMessage
    {
        return new class($this->serviceConnectorMock, $this->registrationMock, $body) extends LaunchMessage
        {
            public static function requiredClaims(): array
            {
                return [Claim::VERSION, Claim::DEPLOYMENT_ID, Claim::MESSAGE_TYPE];
            }
        };
    }
}
