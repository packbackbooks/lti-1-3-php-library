<?php

namespace Tests\Messages;

use Mockery;
use Packback\Lti1p3\Claims\Activity;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\DeploymentId;
use Packback\Lti1p3\Claims\Version;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\Messages\LtiMessage;
use Tests\TestCase;

class LtiMessageTest extends TestCase
{
    private $serviceConnectorMock;
    private $registrationMock;
    private $testMessage;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serviceConnectorMock = Mockery::mock(ILtiServiceConnector::class);
        $this->registrationMock = Mockery::mock(ILtiRegistration::class);
    }

    private function createTestMessage(array $body = []): LtiMessage
    {
        return new class($this->serviceConnectorMock, $this->registrationMock, $body) extends LtiMessage
        {
            public static function requiredClaims(): array
            {
                return [Claim::VERSION, Claim::DEPLOYMENT_ID];
            }
        };
    }

    public function test_get_body_returns_body()
    {
        $body = [
            'iss' => 'https://example.com',
            'aud' => 'client-id',
            'sub' => 'user-123',
        ];
        $message = $this->createTestMessage($body);

        $this->assertEquals($body, $message->getBody());
    }

    public function test_get_aud_returns_string_when_aud_is_string()
    {
        $aud = 'client-id-123';
        $body = ['aud' => $aud];
        $message = $this->createTestMessage($body);

        $this->assertEquals($aud, $message->getAud());
    }

    public function test_get_aud_returns_first_element_when_aud_is_array()
    {
        $audArray = ['client-id-123', 'client-id-456'];
        $body = ['aud' => $audArray];
        $message = $this->createTestMessage($body);

        $this->assertEquals('client-id-123', $message->getAud());
    }

    public function test_has_claim_returns_true_when_claim_exists()
    {
        $body = [Claim::ACTIVITY => ['id' => 'activity-123']];
        $message = $this->createTestMessage($body);

        $this->assertTrue($message->hasClaim(Activity::class));
    }

    public function test_has_claim_returns_false_when_claim_missing()
    {
        $body = ['other_claim' => 'value'];
        $message = $this->createTestMessage($body);

        $this->assertFalse($message->hasClaim(Activity::class));
    }

    public function test_deployment_id_claim_returns_deployment_id_instance()
    {
        $deploymentId = 'deployment-123';
        $body = [Claim::DEPLOYMENT_ID => $deploymentId];
        $message = $this->createTestMessage($body);

        $deploymentIdClaim = $message->deploymentIdClaim();

        $this->assertInstanceOf(DeploymentId::class, $deploymentIdClaim);
        $this->assertEquals($deploymentId, $deploymentIdClaim->getBody());
    }

    public function test_version_claim_returns_version_instance()
    {
        $version = '1.3.0';
        $body = [Claim::VERSION => $version];
        $message = $this->createTestMessage($body);

        $versionClaim = $message->versionClaim();

        $this->assertInstanceOf(Version::class, $versionClaim);
        $this->assertEquals($version, $versionClaim->getBody());
    }
}
