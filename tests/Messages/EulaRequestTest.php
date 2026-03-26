<?php

namespace Tests\Messages;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\EulaService;
use Packback\Lti1p3\Claims\MessageType;
use Packback\Lti1p3\Claims\Roles;
use Packback\Lti1p3\Claims\TargetLinkUri;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\Messages\EulaRequest;
use Tests\TestCase;

class EulaRequestTest extends TestCase
{
    private $serviceConnectorMock;
    private $registrationMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serviceConnectorMock = Mockery::mock(ILtiServiceConnector::class);
        $this->registrationMock = Mockery::mock(ILtiRegistration::class);
    }

    public function test_message_type_returns_eula_constant()
    {
        $this->assertEquals('LtiEulaRequest', EulaRequest::messageType());
        $this->assertEquals(LtiConstants::MESSAGE_TYPE_EULA, EulaRequest::messageType());
    }

    public function test_required_claims_returns_expected_claims()
    {
        $expectedClaims = [
            MessageType::claimKey(),
            TargetLinkUri::claimKey(),
            Roles::claimKey(),
            EulaService::claimKey(),
        ];

        $this->assertEquals($expectedClaims, EulaRequest::requiredClaims());
    }

    public function test_eula_service_claim_returns_eula_service_instance()
    {
        $eulaService = ['service_url' => 'https://example.com/eula', 'version' => '1.0'];
        $body = [Claim::EULASERVICE => $eulaService];
        $message = new EulaRequest($this->serviceConnectorMock, $this->registrationMock, $body);

        $eulaServiceClaim = $message->eulaServiceClaim();

        $this->assertInstanceOf(EulaService::class, $eulaServiceClaim);
        $this->assertEquals($eulaService, $eulaServiceClaim->getBody());
    }

    public function test_get_launch_id_returns_unique_string()
    {
        $message = new EulaRequest($this->serviceConnectorMock, $this->registrationMock, []);

        $launchId = $message->getLaunchId();

        $this->assertStringStartsWith('lti1p3_launch_', $launchId);
    }
}
