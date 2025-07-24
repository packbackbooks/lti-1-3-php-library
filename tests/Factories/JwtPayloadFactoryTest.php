<?php

namespace Tests\Factories;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\DeploymentId;
use Packback\Lti1p3\Claims\Roles;
use Packback\Lti1p3\Claims\Version;
use Packback\Lti1p3\Factories\JwtPayloadFactory;
use Packback\Lti1p3\Interfaces\IDatabase;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\LtiDeployment;
use Packback\Lti1p3\LtiException;
use Packback\Lti1p3\Messages\AssetProcessorSettingsRequest;
use Packback\Lti1p3\Messages\AssetProcessorSubmissionNotice;
use Packback\Lti1p3\Messages\ContextCopyNotice;
use Packback\Lti1p3\Messages\DeepLinkingRequest;
use Packback\Lti1p3\Messages\EulaRequest;
use Packback\Lti1p3\Messages\HelloWorldNotice;
use Packback\Lti1p3\Messages\LtiMessage;
use Packback\Lti1p3\Messages\ReportReviewRequest;
use Packback\Lti1p3\Messages\ResourceLinkRequest;
use Tests\TestCase;

class JwtPayloadFactoryTest extends TestCase
{
    private $factoryMock;
    private $databaseMock;
    private $serviceConnectorMock;
    private $registrationMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->databaseMock = Mockery::mock(IDatabase::class);
        $this->serviceConnectorMock = Mockery::mock(ILtiServiceConnector::class);
        $this->registrationMock = Mockery::mock(ILtiRegistration::class);

        $this->factoryMock = new class($this->databaseMock, $this->serviceConnectorMock) extends JwtPayloadFactory
        {
            public static function getTypeClaim(): string
            {
                return Claim::MESSAGE_TYPE;
            }

            protected static function getTokenKey(): string
            {
                return 'id_token';
            }

            public function create(array $message): LtiMessage
            {
                return new DeepLinkingRequest($this->serviceConnector, Mockery::mock(ILtiRegistration::class), []);
            }

            public function getTypeName($jwt): string
            {
                return $jwt['body'][self::getTypeClaim()];
            }

            protected function validateState(array $message): static
            {
                return $this;
            }

            protected function validateNonce(array $jwt, array $message): static
            {
                return $this;
            }
        };
    }

    public function test_get_missing_registration_error_msg_with_client_id()
    {
        $issuerUrl = 'https://test.issuer.com';
        $clientId = 'test-client-id';

        $errorMsg = JwtPayloadFactory::getMissingRegistrationErrorMsg($issuerUrl, $clientId);

        $this->assertStringContainsString($issuerUrl, $errorMsg);
        $this->assertStringContainsString($clientId, $errorMsg);
        $this->assertStringContainsString('LTI 1.3 Registration not found', $errorMsg);
    }

    public function test_get_missing_registration_error_msg_with_null_client_id()
    {
        $issuerUrl = 'https://test.issuer.com';
        $clientId = null;

        $errorMsg = JwtPayloadFactory::getMissingRegistrationErrorMsg($issuerUrl, $clientId);

        $this->assertStringContainsString($issuerUrl, $errorMsg);
        $this->assertStringContainsString('(N/A)', $errorMsg);
        $this->assertStringContainsString('LTI 1.3 Registration not found', $errorMsg);
    }

    public function test_create_message_returns_deep_linking_request()
    {
        $jwt = ['body' => [Claim::MESSAGE_TYPE => LtiConstants::MESSAGE_TYPE_DEEPLINK]];

        $message = $this->factoryMock->createMessage($this->registrationMock, $jwt);

        $this->assertInstanceOf(DeepLinkingRequest::class, $message);
    }

    public function test_create_message_returns_resource_link_request()
    {
        $jwt = ['body' => [Claim::MESSAGE_TYPE => LtiConstants::MESSAGE_TYPE_RESOURCE]];

        $message = $this->factoryMock->createMessage($this->registrationMock, $jwt);

        $this->assertInstanceOf(ResourceLinkRequest::class, $message);
    }

    public function test_create_message_returns_eula_request()
    {
        $jwt = ['body' => [Claim::MESSAGE_TYPE => LtiConstants::MESSAGE_TYPE_EULA]];

        $message = $this->factoryMock->createMessage($this->registrationMock, $jwt);

        $this->assertInstanceOf(EulaRequest::class, $message);
    }

    public function test_create_message_returns_report_review_request()
    {
        $jwt = ['body' => [Claim::MESSAGE_TYPE => LtiConstants::MESSAGE_TYPE_REPORTREVIEW]];

        $message = $this->factoryMock->createMessage($this->registrationMock, $jwt);

        $this->assertInstanceOf(ReportReviewRequest::class, $message);
    }

    public function test_create_message_returns_asset_processor_settings_request()
    {
        $jwt = ['body' => [Claim::MESSAGE_TYPE => LtiConstants::MESSAGE_TYPE_ASSETPROCESSORSETTINGS]];

        $message = $this->factoryMock->createMessage($this->registrationMock, $jwt);

        $this->assertInstanceOf(AssetProcessorSettingsRequest::class, $message);
    }

    public function test_create_message_returns_hello_world_notice()
    {
        $jwt = ['body' => [Claim::MESSAGE_TYPE => LtiConstants::NOTICE_TYPE_HELLOWORLD]];

        $message = $this->factoryMock->createMessage($this->registrationMock, $jwt);

        $this->assertInstanceOf(HelloWorldNotice::class, $message);
    }

    public function test_create_message_returns_context_copy_notice()
    {
        $jwt = ['body' => [Claim::MESSAGE_TYPE => LtiConstants::NOTICE_TYPE_CONTEXTCOPY]];

        $message = $this->factoryMock->createMessage($this->registrationMock, $jwt);

        $this->assertInstanceOf(ContextCopyNotice::class, $message);
    }

    public function test_create_message_returns_asset_processor_submission_notice()
    {
        $jwt = ['body' => [Claim::MESSAGE_TYPE => LtiConstants::NOTICE_TYPE_ASSETPROCESSORSUBMISSION]];

        $message = $this->factoryMock->createMessage($this->registrationMock, $jwt);

        $this->assertInstanceOf(AssetProcessorSubmissionNotice::class, $message);
    }

    public function test_create_message_throws_exception_for_invalid_message_type()
    {
        $jwt = ['body' => [Claim::MESSAGE_TYPE => 'InvalidMessageType']];

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(JwtPayloadFactory::ERR_INVALID_MESSAGE_TYPE);

        $this->factoryMock->createMessage($this->registrationMock, $jwt);
    }

    public function test_validate_jwt_format_throws_exception_for_missing_token()
    {
        $message = ['other_field' => 'value'];

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(JwtPayloadFactory::ERR_MISSING_ID_TOKEN);

        $this->factoryMock->validate($message);
    }

    public function test_validate_jwt_format_throws_exception_for_invalid_jwt_parts()
    {
        $message = ['id_token' => 'invalid.jwt'];

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(JwtPayloadFactory::ERR_INVALID_ID_TOKEN);

        $this->factoryMock->validate($message);
    }

    public function test_validate_registration_throws_exception_for_missing_registration()
    {
        $jwtBody = [
            Version::claimKey() => LtiConstants::V1_3,
            'iss' => 'https://test.issuer.com',
            'aud' => 'test-client-id',
            'sub' => 'test-user',
            DeploymentId::claimKey() => 'test-deployment',
            Roles::claimKey() => ['Instructor'],
            Claim::MESSAGE_TYPE => LtiConstants::MESSAGE_TYPE_RESOURCE,
        ];

        $jwt = $this->createJwtToken($jwtBody);
        $message = ['id_token' => $jwt];

        $this->databaseMock->shouldReceive('findRegistrationByIssuer')
            ->with('https://test.issuer.com', 'test-client-id')
            ->andReturn(null);

        $this->expectException(LtiException::class);
        $this->expectExceptionMessageMatches('/LTI 1.3 Registration not found/');

        $this->factoryMock->validate($message);
    }

    public function test_validate_registration_throws_exception_for_client_not_registered()
    {
        $jwtBody = [
            Version::claimKey() => LtiConstants::V1_3,
            'iss' => 'https://test.issuer.com',
            'aud' => 'test-client-id',
            'sub' => 'test-user',
            DeploymentId::claimKey() => 'test-deployment',
            Roles::claimKey() => ['Instructor'],
            Claim::MESSAGE_TYPE => LtiConstants::MESSAGE_TYPE_RESOURCE,
        ];

        $jwt = $this->createJwtToken($jwtBody);
        $message = ['id_token' => $jwt];

        $this->registrationMock->shouldReceive('getClientId')->andReturn('different-client-id');

        $this->databaseMock->shouldReceive('findRegistrationByIssuer')
            ->with('https://test.issuer.com', 'test-client-id')
            ->andReturn($this->registrationMock);

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(JwtPayloadFactory::ERR_CLIENT_NOT_REGISTERED);

        $this->factoryMock->validate($message);
    }

    public function test_validate_jwt_signature_throws_exception_for_missing_kid()
    {
        $jwtBody = [
            Version::claimKey() => LtiConstants::V1_3,
            'iss' => 'https://test.issuer.com',
            'aud' => 'test-client-id',
            'sub' => 'test-user',
            DeploymentId::claimKey() => 'test-deployment',
            Roles::claimKey() => ['Instructor'],
            Claim::MESSAGE_TYPE => LtiConstants::MESSAGE_TYPE_RESOURCE,
        ];

        $jwt = $this->createJwtToken($jwtBody, ['typ' => 'JWT', 'alg' => 'RS256']);
        $message = ['id_token' => $jwt];

        $this->registrationMock->shouldReceive('getClientId')->andReturn('test-client-id');

        $this->databaseMock->shouldReceive('findRegistrationByIssuer')
            ->with('https://test.issuer.com', 'test-client-id')
            ->andReturn($this->registrationMock);

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(JwtPayloadFactory::ERR_NO_KID);

        $this->factoryMock->validate($message);
    }

    public function test_validate_required_claims_throws_exception_for_missing_version()
    {
        $jwtBody = [
            'iss' => 'https://test.issuer.com',
            'aud' => 'test-client-id',
            'sub' => 'test-user',
            DeploymentId::claimKey() => 'test-deployment',
            Roles::claimKey() => ['Instructor'],
            Claim::MESSAGE_TYPE => LtiConstants::MESSAGE_TYPE_RESOURCE,
        ];

        $jwt = ['body' => $jwtBody];

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage('Missing required claim: '.Version::claimKey());

        $this->invokeMethod($this->factoryMock, 'validateRequiredClaims', [$jwt]);
    }

    public function test_validate_required_claims_throws_exception_for_incorrect_version()
    {
        $jwtBody = [
            Version::claimKey() => '1.0.0',
            'iss' => 'https://test.issuer.com',
            'aud' => 'test-client-id',
            'sub' => 'test-user',
            DeploymentId::claimKey() => 'test-deployment',
            Roles::claimKey() => ['Instructor'],
            Claim::MESSAGE_TYPE => LtiConstants::MESSAGE_TYPE_RESOURCE,
        ];

        $jwt = ['body' => $jwtBody];

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage('Incorrect version, expected 1.3.0');

        $this->invokeMethod($this->factoryMock, 'validateRequiredClaims', [$jwt]);
    }

    public function test_validate_required_claims_throws_exception_for_empty_sub()
    {
        $jwtBody = [
            Version::claimKey() => LtiConstants::V1_3,
            'iss' => 'https://test.issuer.com',
            'aud' => 'test-client-id',
            'sub' => '',
            DeploymentId::claimKey() => 'test-deployment',
            Roles::claimKey() => ['Instructor'],
            Claim::MESSAGE_TYPE => LtiConstants::MESSAGE_TYPE_RESOURCE,
        ];

        $jwt = ['body' => $jwtBody];

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage('Invalid claim: sub cannot be empty');

        $this->invokeMethod($this->factoryMock, 'validateRequiredClaims', [$jwt]);
    }

    public function test_validate_deployment_returns_deployment()
    {
        $deployment = new LtiDeployment('test-deployment');
        $jwtBody = [
            'iss' => 'https://test.issuer.com',
            'aud' => 'test-client-id',
            DeploymentId::claimKey() => 'test-deployment',
        ];

        $this->databaseMock->shouldReceive('findDeployment')
            ->with('https://test.issuer.com', 'test-deployment', 'test-client-id')
            ->andReturn($deployment);

        $jwt = ['body' => $jwtBody];
        $result = $this->invokeMethod($this->factoryMock, 'validateDeployment', [$jwt]);

        $this->assertSame($deployment, $result);
    }

    public function test_get_aud_returns_string_when_aud_is_string()
    {
        $jwt = ['body' => ['aud' => 'test-client-id']];

        $aud = $this->invokeMethod($this->factoryMock, 'getAud', [$jwt]);

        $this->assertEquals('test-client-id', $aud);
    }

    public function test_get_aud_returns_first_element_when_aud_is_array()
    {
        $jwt = ['body' => ['aud' => ['test-client-id', 'other-client-id']]];

        $aud = $this->invokeMethod($this->factoryMock, 'getAud', [$jwt]);

        $this->assertEquals('test-client-id', $aud);
    }

    public function test_ensure_deployment_exists_throws_exception_for_null_deployment()
    {
        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(JwtPayloadFactory::ERR_NO_DEPLOYMENT);

        $this->invokeMethod($this->factoryMock, 'ensureDeploymentExists', [null]);
    }

    public function test_ensure_deployment_exists_returns_self_for_valid_deployment()
    {
        $deployment = new LtiDeployment('test-deployment');

        $result = $this->invokeMethod($this->factoryMock, 'ensureDeploymentExists', [$deployment]);

        $this->assertSame($this->factoryMock, $result);
    }

    public function test_get_public_key_throws_exception_for_transfer_error()
    {
        $jwt = [
            'header' => ['kid' => 'test-kid', 'alg' => 'RS256'],
            'body' => ['iss' => 'test-issuer'],
        ];

        $this->registrationMock->shouldReceive('getKeySetUrl')
            ->andReturn('https://example.com/jwks');

        $this->serviceConnectorMock->shouldReceive('makeRequest')
            ->andThrow(new \GuzzleHttp\Exception\TransferException('Network error'));

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(JwtPayloadFactory::ERR_NO_PUBLIC_KEY);

        $this->invokeMethod($this->factoryMock, 'getPublicKey', [$this->registrationMock, $jwt]);
    }

    public function test_get_public_key_throws_exception_for_empty_key_set()
    {
        $jwt = [
            'header' => ['kid' => 'test-kid', 'alg' => 'RS256'],
            'body' => ['iss' => 'test-issuer'],
        ];

        $responseMock = Mockery::mock(\Psr\Http\Message\ResponseInterface::class);

        $this->registrationMock->shouldReceive('getKeySetUrl')
            ->andReturn('https://example.com/jwks');

        $this->serviceConnectorMock->shouldReceive('makeRequest')
            ->andReturn($responseMock);

        $this->serviceConnectorMock->shouldReceive('getResponseBody')
            ->with($responseMock)
            ->andReturn([]);

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(JwtPayloadFactory::ERR_FETCH_PUBLIC_KEY);

        $this->invokeMethod($this->factoryMock, 'getPublicKey', [$this->registrationMock, $jwt]);
    }

    public function test_get_public_key_throws_exception_for_no_matching_kid()
    {
        $jwt = [
            'header' => ['kid' => 'missing-kid', 'alg' => 'RS256'],
            'body' => ['iss' => 'test-issuer'],
        ];

        $keySet = [
            'keys' => [
                [
                    'kid' => 'different-kid',
                    'kty' => 'RSA',
                    'use' => 'sig',
                    'n' => 'test-n',
                    'e' => 'AQAB',
                ],
            ],
        ];

        $responseMock = Mockery::mock(\Psr\Http\Message\ResponseInterface::class);

        $this->registrationMock->shouldReceive('getKeySetUrl')
            ->andReturn('https://example.com/jwks');

        $this->serviceConnectorMock->shouldReceive('makeRequest')
            ->andReturn($responseMock);

        $this->serviceConnectorMock->shouldReceive('getResponseBody')
            ->with($responseMock)
            ->andReturn($keySet);

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(JwtPayloadFactory::ERR_NO_MATCHING_PUBLIC_KEY);

        $this->invokeMethod($this->factoryMock, 'getPublicKey', [$this->registrationMock, $jwt]);
    }

    public function test_get_public_key_succeeds_with_matching_kid_and_explicit_alg()
    {
        $jwt = [
            'header' => ['kid' => 'test-kid', 'alg' => 'RS256'],
            'body' => ['iss' => 'test-issuer'],
        ];

        $keySet = [
            'keys' => [
                [
                    'kid' => 'test-kid',
                    'kty' => 'RSA',
                    'alg' => 'RS256',
                    'use' => 'sig',
                    'n' => 'test-modulus-value',
                    'e' => 'AQAB',
                ],
            ],
        ];

        $responseMock = Mockery::mock(\Psr\Http\Message\ResponseInterface::class);

        $this->registrationMock->shouldReceive('getKeySetUrl')
            ->andReturn('https://example.com/jwks');

        $this->serviceConnectorMock->shouldReceive('makeRequest')
            ->andReturn($responseMock);

        $this->serviceConnectorMock->shouldReceive('getResponseBody')
            ->with($responseMock)
            ->andReturn($keySet);

        $result = $this->invokeMethod($this->factoryMock, 'getPublicKey', [$this->registrationMock, $jwt]);

        $this->assertInstanceOf(\Firebase\JWT\Key::class, $result);
    }

    public function test_get_public_key_succeeds_with_matching_kid_and_inferred_alg()
    {
        $jwt = [
            'header' => ['kid' => 'test-kid', 'alg' => 'RS256'],
            'body' => ['iss' => 'test-issuer'],
        ];

        $keySet = [
            'keys' => [
                [
                    'kid' => 'test-kid',
                    'kty' => 'RSA',
                    'use' => 'sig',
                    'n' => 'test-modulus-value',
                    'e' => 'AQAB',
                ],
            ],
        ];

        $responseMock = Mockery::mock(\Psr\Http\Message\ResponseInterface::class);

        $this->registrationMock->shouldReceive('getKeySetUrl')
            ->andReturn('https://example.com/jwks');

        $this->serviceConnectorMock->shouldReceive('makeRequest')
            ->andReturn($responseMock);

        $this->serviceConnectorMock->shouldReceive('getResponseBody')
            ->with($responseMock)
            ->andReturn($keySet);

        $result = $this->invokeMethod($this->factoryMock, 'getPublicKey', [$this->registrationMock, $jwt]);

        $this->assertInstanceOf(\Firebase\JWT\Key::class, $result);
    }

    public function test_get_public_key_with_multiple_keys_finds_correct_one()
    {
        $jwt = [
            'header' => ['kid' => 'correct-kid', 'alg' => 'RS256'],
            'body' => ['iss' => 'test-issuer'],
        ];

        $keySet = [
            'keys' => [
                [
                    'kid' => 'wrong-kid',
                    'kty' => 'RSA',
                    'alg' => 'RS256',
                    'use' => 'sig',
                    'n' => 'wrong-modulus',
                    'e' => 'AQAB',
                ],
                [
                    'kid' => 'correct-kid',
                    'kty' => 'RSA',
                    'alg' => 'RS256',
                    'use' => 'sig',
                    'n' => 'correct-modulus',
                    'e' => 'AQAB',
                ],
            ],
        ];

        $responseMock = Mockery::mock(\Psr\Http\Message\ResponseInterface::class);

        $this->registrationMock->shouldReceive('getKeySetUrl')
            ->andReturn('https://example.com/jwks');

        $this->serviceConnectorMock->shouldReceive('makeRequest')
            ->andReturn($responseMock);

        $this->serviceConnectorMock->shouldReceive('getResponseBody')
            ->with($responseMock)
            ->andReturn($keySet);

        $result = $this->invokeMethod($this->factoryMock, 'getPublicKey', [$this->registrationMock, $jwt]);

        $this->assertInstanceOf(\Firebase\JWT\Key::class, $result);
    }

    public function test_get_public_key_with_ec_algorithm()
    {
        $jwt = [
            'header' => ['kid' => 'ec-key', 'alg' => 'ES256'],
            'body' => ['iss' => 'test-issuer'],
        ];

        $keySet = [
            'keys' => [
                [
                    'kid' => 'ec-key',
                    'kty' => 'EC',
                    'alg' => 'ES256',
                    'use' => 'sig',
                    'crv' => 'P-256',
                    'x' => 'test-x-coordinate',
                    'y' => 'test-y-coordinate',
                ],
            ],
        ];

        $responseMock = Mockery::mock(\Psr\Http\Message\ResponseInterface::class);

        $this->registrationMock->shouldReceive('getKeySetUrl')
            ->andReturn('https://example.com/jwks');

        $this->serviceConnectorMock->shouldReceive('makeRequest')
            ->andReturn($responseMock);

        $this->serviceConnectorMock->shouldReceive('getResponseBody')
            ->with($responseMock)
            ->andReturn($keySet);

        $result = $this->invokeMethod($this->factoryMock, 'getPublicKey', [$this->registrationMock, $jwt]);

        $this->assertInstanceOf(\Firebase\JWT\Key::class, $result);
    }

    public function test_get_key_algorithm_returns_explicit_alg_from_key()
    {
        $key = ['alg' => 'RS512', 'kty' => 'RSA'];
        $jwt = ['header' => ['alg' => 'RS256']];

        $result = $this->invokeMethod($this->factoryMock, 'getKeyAlgorithm', [$key, $jwt]);

        $this->assertEquals('RS512', $result);
    }

    public function test_get_key_algorithm_infers_alg_from_jwt_header_when_missing()
    {
        $key = ['kty' => 'RSA'];
        $jwt = ['header' => ['alg' => 'RS256']];

        $result = $this->invokeMethod($this->factoryMock, 'getKeyAlgorithm', [$key, $jwt]);

        $this->assertEquals('RS256', $result);
    }

    public function test_get_key_algorithm_throws_exception_for_mismatched_alg_key()
    {
        $key = ['kty' => 'EC'];
        $jwt = ['header' => ['alg' => 'RS256']];

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(JwtPayloadFactory::ERR_MISMATCHED_ALG_KEY);

        $this->invokeMethod($this->factoryMock, 'getKeyAlgorithm', [$key, $jwt]);
    }

    public function test_jwt_alg_matches_jwk_kty_returns_true_for_rsa_match()
    {
        $jwt = ['header' => ['alg' => 'RS256']];
        $key = ['kty' => 'RSA'];

        $result = $this->invokeMethod($this->factoryMock, 'jwtAlgMatchesJwkKty', [$jwt, $key]);

        $this->assertTrue($result);
    }

    public function test_jwt_alg_matches_jwk_kty_returns_true_for_ec_match()
    {
        $jwt = ['header' => ['alg' => 'ES256']];
        $key = ['kty' => 'EC'];

        $result = $this->invokeMethod($this->factoryMock, 'jwtAlgMatchesJwkKty', [$jwt, $key]);

        $this->assertTrue($result);
    }

    public function test_jwt_alg_matches_jwk_kty_returns_false_for_mismatch()
    {
        $jwt = ['header' => ['alg' => 'RS256']];
        $key = ['kty' => 'EC'];

        $result = $this->invokeMethod($this->factoryMock, 'jwtAlgMatchesJwkKty', [$jwt, $key]);

        $this->assertFalse($result);
    }

    public function test_jwt_alg_matches_jwk_kty_returns_false_for_unsupported_alg()
    {
        $jwt = ['header' => ['alg' => 'HS256']];
        $key = ['kty' => 'oct'];

        $result = $this->invokeMethod($this->factoryMock, 'jwtAlgMatchesJwkKty', [$jwt, $key]);

        $this->assertFalse($result);
    }

    public function test_jwt_alg_matches_jwk_kty_handles_all_supported_algorithms()
    {
        $supportedMappings = [
            'RS256' => 'RSA',
            'RS384' => 'RSA',
            'RS512' => 'RSA',
            'ES256' => 'EC',
            'ES384' => 'EC',
            'ES512' => 'EC',
        ];

        foreach ($supportedMappings as $alg => $expectedKty) {
            $jwt = ['header' => ['alg' => $alg]];
            $key = ['kty' => $expectedKty];

            $result = $this->invokeMethod($this->factoryMock, 'jwtAlgMatchesJwkKty', [$jwt, $key]);

            $this->assertTrue($result, "Algorithm $alg should match key type $expectedKty");
        }
    }

    private function createJwtToken(array $body, ?array $header = null): string
    {
        $defaultHeader = ['typ' => 'JWT', 'alg' => 'RS256', 'kid' => 'test-kid'];
        $header = $header ?? $defaultHeader;

        $encodedHeader = $this->base64UrlEncode(json_encode($header));
        $encodedBody = $this->base64UrlEncode(json_encode($body));
        $signature = $this->base64UrlEncode('fake-signature');

        return $encodedHeader.'.'.$encodedBody.'.'.$signature;
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function invokeMethod($object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
