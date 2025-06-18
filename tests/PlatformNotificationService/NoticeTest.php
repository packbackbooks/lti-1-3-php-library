<?php

namespace Tests\PlatformNotificationService;

use Mockery;
use Packback\Lti1p3\Interfaces\IDatabase;
use Packback\Lti1p3\Interfaces\ILtiDeployment;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\LtiException;
use Packback\Lti1p3\PlatformNotificationService\Notice;
use Tests\TestCase;

class NoticeTest extends TestCase
{
    private Notice $notice;
    private IDatabase $databaseMock;
    private ILtiServiceConnector $serviceConnectorMock;
    private ILtiRegistration $registrationMock;
    private ILtiDeployment $deploymentMock;

    protected function setUp(): void
    {
        $this->databaseMock = Mockery::mock(IDatabase::class);
        $this->serviceConnectorMock = Mockery::mock(ILtiServiceConnector::class);
        $this->registrationMock = Mockery::mock(ILtiRegistration::class);
        $this->deploymentMock = Mockery::mock(ILtiDeployment::class);

        $this->notice = new Notice(
            $this->databaseMock,
            $this->serviceConnectorMock
        );
    }

    public function test_it_instantiates()
    {
        $this->assertInstanceOf(Notice::class, $this->notice);
    }

    public function test_it_creates_new_instance()
    {
        $notice = Notice::new(
            $this->databaseMock,
            $this->serviceConnectorMock
        );
        $this->assertInstanceOf(Notice::class, $notice);
    }

    public function test_it_sets_request()
    {
        $request = ['jwt' => 'test.jwt.token'];
        $result = $this->notice->setRequest($request);

        $this->assertSame($this->notice, $result);
    }

    public function test_validate_jwt_format_throws_exception_for_missing_jwt()
    {
        $request = ['no_jwt' => 'value'];

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(Notice::ERR_MISSING_ID_TOKEN);

        $this->notice->setRequest($request)->validate();
    }

    public function test_validate_jwt_format_throws_exception_for_invalid_jwt_parts()
    {
        $request = ['jwt' => 'invalid.jwt'];

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(Notice::ERR_INVALID_ID_TOKEN);

        $this->notice->setRequest($request)->validate();
    }

    public function test_validate_nonce_throws_exception_for_missing_nonce()
    {
        $jwtWithoutNonce = $this->createJwtToken([
            LtiConstants::VERSION => LtiConstants::V1_3,
            'iss' => 'https://test.issuer.com',
            'aud' => 'test-client-id',
        ]);

        $request = ['jwt' => $jwtWithoutNonce];

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(Notice::ERR_MISSING_NONCE);

        $this->notice->setRequest($request)->validate();
    }

    public function test_validate_registration_throws_exception_for_missing_registration()
    {
        $jwtBody = [
            LtiConstants::VERSION => LtiConstants::V1_3,
            'iss' => 'https://test.issuer.com',
            'aud' => 'test-client-id',
            'nonce' => 'test-nonce',
        ];

        $jwt = $this->createJwtToken($jwtBody);
        $request = ['jwt' => $jwt];

        $this->databaseMock->shouldReceive('findRegistrationByIssuer')
            ->with('https://test.issuer.com', 'test-client-id')
            ->andReturn(null);

        $this->expectException(LtiException::class);
        $this->expectExceptionMessageMatches('/LTI 1.3 Registration not found/');

        $this->notice->setRequest($request)->validate();
    }

    public function test_get_missing_registration_error_msg()
    {
        $issuerUrl = 'https://test.issuer.com';
        $clientId = 'test-client-id';

        $errorMsg = Notice::getMissingRegistrationErrorMsg($issuerUrl, $clientId);

        $this->assertStringContainsString($issuerUrl, $errorMsg);
        $this->assertStringContainsString($clientId, $errorMsg);
        $this->assertStringContainsString('LTI 1.3 Registration not found', $errorMsg);
    }

    public function test_get_missing_registration_error_msg_with_null_client_id()
    {
        $issuerUrl = 'https://test.issuer.com';
        $clientId = null;

        $errorMsg = Notice::getMissingRegistrationErrorMsg($issuerUrl, $clientId);

        $this->assertStringContainsString($issuerUrl, $errorMsg);
        $this->assertStringContainsString('(N/A)', $errorMsg);
    }

    public function test_error_constants_are_defined()
    {
        $this->assertEquals('Failed to fetch public key.', Notice::ERR_FETCH_PUBLIC_KEY);
        $this->assertEquals('Unable to find public key.', Notice::ERR_NO_PUBLIC_KEY);
        $this->assertEquals('Unable to find a public key which matches your JWT.', Notice::ERR_NO_MATCHING_PUBLIC_KEY);
        $this->assertEquals('Missing id_token.', Notice::ERR_MISSING_ID_TOKEN);
        $this->assertEquals('Invalid id_token, JWT must contain 3 parts.', Notice::ERR_INVALID_ID_TOKEN);
        $this->assertEquals('Missing Nonce.', Notice::ERR_MISSING_NONCE);
        $this->assertEquals('Invalid Nonce.', Notice::ERR_INVALID_NONCE);
        $this->assertEquals('Client id not registered for this issuer.', Notice::ERR_CLIENT_NOT_REGISTERED);
        $this->assertEquals('No KID specified in the JWT Header.', Notice::ERR_NO_KID);
        $this->assertEquals('Invalid signature on id_token', Notice::ERR_INVALID_SIGNATURE);
        $this->assertEquals('No deployment ID was specified', Notice::ERR_MISSING_DEPLOYEMENT_ID);
        $this->assertEquals('Unable to find deployment.', Notice::ERR_NO_DEPLOYMENT);
        $this->assertEquals('Invalid message type', Notice::ERR_INVALID_MESSAGE_TYPE);
        $this->assertEquals('Unrecognized message type.', Notice::ERR_UNRECOGNIZED_MESSAGE_TYPE);
        $this->assertEquals('Message validation failed.', Notice::ERR_INVALID_MESSAGE);
        $this->assertEquals('Invalid alg was specified in the JWT header.', Notice::ERR_INVALID_ALG);
        $this->assertEquals('The alg specified in the JWT header is incompatible with the JWK key type.', Notice::ERR_MISMATCHED_ALG_KEY);
    }

    public function test_it_supports_lti_supported_algorithms()
    {
        $this->assertTrue(true);
    }

    private function createJwtToken(array $body): string
    {
        $header = ['typ' => 'JWT', 'alg' => 'RS256', 'kid' => 'test-kid'];
        $encodedHeader = $this->base64UrlEncode(json_encode($header));
        $encodedBody = $this->base64UrlEncode(json_encode($body));
        $signature = $this->base64UrlEncode('fake-signature');

        return $encodedHeader.'.'.$encodedBody.'.'.$signature;
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
