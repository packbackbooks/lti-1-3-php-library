<?php

namespace Tests\Messages;

use Mockery;
use Packback\Lti1p3\Interfaces\IDatabase;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\LtiException;
use Packback\Lti1p3\Messages\Notice;
use Tests\TestCase;

class NoticeTest extends TestCase
{
    private Notice $notice;
    private $databaseMock;
    private $serviceConnectorMock;

    protected function setUp(): void
    {
        $this->databaseMock = Mockery::mock(IDatabase::class);
        $this->serviceConnectorMock = Mockery::mock(ILtiServiceConnector::class);

        $this->notice = new Notice(
            $this->databaseMock,
            $this->serviceConnectorMock
        );
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
        $message = ['jwt' => 'test.jwt.token'];
        $result = $this->notice->setMessage($message);

        $this->assertSame($this->notice, $result);
    }

    public function test_validate_jwt_format_throws_exception_for_missing_jwt()
    {
        $message = ['no_jwt' => 'value'];

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(Notice::ERR_MISSING_ID_TOKEN);

        $this->notice->setMessage($message)->validate();
    }

    public function test_validate_jwt_format_throws_exception_for_invalid_jwt_parts()
    {
        $message = ['jwt' => 'invalid.jwt'];

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(Notice::ERR_INVALID_ID_TOKEN);

        $this->notice->setMessage($message)->validate();
    }

    public function test_validate_nonce_throws_exception_for_missing_nonce()
    {
        $jwtWithoutNonce = $this->createJwtToken([
            LtiConstants::VERSION => LtiConstants::V1_3,
            'iss' => 'https://test.issuer.com',
            'aud' => 'test-client-id',
        ]);

        $message = ['jwt' => $jwtWithoutNonce];

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(Notice::ERR_MISSING_NONCE);

        $this->notice->setMessage($message)->validate();
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
        $message = ['jwt' => $jwt];

        $this->databaseMock->shouldReceive('findRegistrationByIssuer')
            ->with('https://test.issuer.com', 'test-client-id')
            ->andReturn(null);

        $this->expectException(LtiException::class);
        $this->expectExceptionMessageMatches('/LTI 1.3 Registration not found/');

        $this->notice->setMessage($message)->validate();
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
