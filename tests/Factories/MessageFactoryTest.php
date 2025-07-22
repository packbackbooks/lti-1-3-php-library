<?php

namespace Tests\Factories;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Factories\MessageFactory;
use Packback\Lti1p3\Interfaces\ICache;
use Packback\Lti1p3\Interfaces\ICookie;
use Packback\Lti1p3\Interfaces\IDatabase;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\LtiException;
use Tests\TestCase;

class MessageFactoryTest extends TestCase
{
    private MessageFactory $messageFactory;
    private $databaseMock;
    private $serviceConnectorMock;
    private $cacheMock;
    private $cookieMock;

    protected function setUp(): void
    {
        $this->databaseMock = Mockery::mock(IDatabase::class);
        $this->serviceConnectorMock = Mockery::mock(ILtiServiceConnector::class);
        $this->cacheMock = Mockery::mock(ICache::class);
        $this->cookieMock = Mockery::mock(ICookie::class);

        $this->messageFactory = new MessageFactory(
            $this->databaseMock,
            $this->serviceConnectorMock,
            $this->cacheMock,
            $this->cookieMock,
        );
    }

    public function test_it_creates_new_instance()
    {
        $this->assertInstanceOf(MessageFactory::class, $this->messageFactory);
    }

    public function test_validate_jwt_format_throws_exception_for_missing_jwt()
    {
        $message = ['no_jwt' => 'value'];

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(MessageFactory::ERR_MISSING_ID_TOKEN);

        $this->messageFactory->validate($message);
    }

    public function test_validate_jwt_format_throws_exception_for_invalid_jwt_parts()
    {
        $message = ['id_token' => 'invalid.jwt'];

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(MessageFactory::ERR_INVALID_ID_TOKEN);

        $this->messageFactory->validate($message);
    }

    public function test_validate_nonce_throws_exception_for_missing_nonce()
    {
        $jwtWithoutNonce = $this->createJwtToken([
            Claim::VERSION => LtiConstants::V1_3,
            'iss' => 'https://test.issuer.com',
            'aud' => 'test-client-id',
        ]);

        $message = ['id_token' => $jwtWithoutNonce];

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(MessageFactory::ERR_MISSING_NONCE);

        $this->messageFactory->validate($message);
    }

    public function test_validate_registration_throws_exception_for_missing_registration()
    {
        $jwtBody = [
            Claim::VERSION => LtiConstants::V1_3,
            'iss' => 'https://test.issuer.com',
            'aud' => 'test-client-id',
            'nonce' => 'test-nonce',
        ];

        $jwt = $this->createJwtToken($jwtBody);
        $message = [
            'id_token' => $jwt,
            'state' => 'test-state',
        ];

        $this->databaseMock->shouldReceive('findRegistrationByIssuer')
            ->with('https://test.issuer.com', 'test-client-id')
            ->andReturn(null);

        $this->cookieMock->shouldReceive('getCookie')
            ->andReturn($message['state']);

        $this->cacheMock->shouldReceive('checkNonceIsValid')
            ->andReturn(true);

        $this->expectException(LtiException::class);
        $this->expectExceptionMessageMatches('/LTI 1.3 Registration not found/');

        $this->messageFactory->validate($message);
    }

    public function test_get_missing_registration_error_msg()
    {
        $issuerUrl = 'https://test.issuer.com';
        $clientId = 'test-client-id';

        $errorMsg = MessageFactory::getMissingRegistrationErrorMsg($issuerUrl, $clientId);

        $this->assertStringContainsString($issuerUrl, $errorMsg);
        $this->assertStringContainsString($clientId, $errorMsg);
        $this->assertStringContainsString('LTI 1.3 Registration not found', $errorMsg);
    }

    public function test_get_missing_registration_error_msg_with_null_client_id()
    {
        $issuerUrl = 'https://test.issuer.com';
        $clientId = null;

        $errorMsg = MessageFactory::getMissingRegistrationErrorMsg($issuerUrl, $clientId);

        $this->assertStringContainsString($issuerUrl, $errorMsg);
        $this->assertStringContainsString('(N/A)', $errorMsg);
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
