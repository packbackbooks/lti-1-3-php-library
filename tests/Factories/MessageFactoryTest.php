<?php

namespace Tests\Factories;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\DeploymentId;
use Packback\Lti1p3\Claims\Lti1p1;
use Packback\Lti1p3\Factories\MessageFactory;
use Packback\Lti1p3\Interfaces\ICache;
use Packback\Lti1p3\Interfaces\ICookie;
use Packback\Lti1p3\Interfaces\IDatabase;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\Interfaces\IMigrationDatabase;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\LtiDeployment;
use Packback\Lti1p3\LtiException;
use Packback\Lti1p3\LtiOidcLogin;
use Packback\Lti1p3\Messages\LaunchMessage;
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

    public function test_get_type_claim_returns_message_type()
    {
        $typeClaim = MessageFactory::getTypeClaim();

        $this->assertEquals(Claim::MESSAGE_TYPE, $typeClaim);
    }

    public function test_get_token_key_returns_id_token()
    {
        $tokenKey = $this->invokeMethod($this->messageFactory, 'getTokenKey');

        $this->assertEquals('id_token', $tokenKey);
    }

    public function test_get_type_name_returns_message_type_from_jwt()
    {
        $jwt = ['body' => [Claim::MESSAGE_TYPE => LtiConstants::MESSAGE_TYPE_RESOURCE]];

        $typeName = $this->messageFactory->getTypeName($jwt);

        $this->assertEquals(LtiConstants::MESSAGE_TYPE_RESOURCE, $typeName);
    }

    public function test_validate_state_throws_exception_for_invalid_state()
    {
        $message = ['state' => 'invalid-state'];

        $this->cookieMock->shouldReceive('getCookie')
            ->with(LtiOidcLogin::COOKIE_PREFIX.'invalid-state')
            ->andReturn('different-state');

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(MessageFactory::ERR_STATE_NOT_FOUND);

        $this->invokeMethod($this->messageFactory, 'validateState', [$message]);
    }

    public function test_validate_state_returns_self_for_valid_state()
    {
        $message = ['state' => 'valid-state'];

        $this->cookieMock->shouldReceive('getCookie')
            ->with(LtiOidcLogin::COOKIE_PREFIX.'valid-state')
            ->andReturn('valid-state');

        $result = $this->invokeMethod($this->messageFactory, 'validateState', [$message]);

        $this->assertSame($this->messageFactory, $result);
    }

    public function test_validate_nonce_throws_exception_for_invalid_nonce()
    {
        $jwt = ['body' => ['nonce' => 'test-nonce']];
        $message = ['state' => 'test-state'];

        $this->cacheMock->shouldReceive('checkNonceIsValid')
            ->with('test-nonce', 'test-state')
            ->andReturn(false);

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(MessageFactory::ERR_INVALID_NONCE);

        $this->invokeMethod($this->messageFactory, 'validateNonce', [$jwt, $message]);
    }

    public function test_validate_nonce_returns_self_for_valid_nonce()
    {
        $jwt = ['body' => ['nonce' => 'test-nonce']];
        $message = ['state' => 'test-state'];

        $this->cacheMock->shouldReceive('checkNonceIsValid')
            ->with('test-nonce', 'test-state')
            ->andReturn(true);

        $result = $this->invokeMethod($this->messageFactory, 'validateNonce', [$jwt, $message]);

        $this->assertSame($this->messageFactory, $result);
    }

    public function test_cache_launch_data_caches_message_data()
    {
        $launchMessage = $this->createMockLaunchMessage();
        $launchId = 'test-launch-id';
        $body = ['test' => 'data'];

        $launchMessage->shouldReceive('getLaunchId')->andReturn($launchId);
        $launchMessage->shouldReceive('getBody')->andReturn($body);

        $this->cacheMock->shouldReceive('cacheLaunchData')
            ->with($launchId, $body)
            ->once();

        $result = $this->messageFactory->cacheLaunchData($launchMessage);

        $this->assertSame($this->messageFactory, $result);
    }

    public function test_can_migrate_returns_true_for_migration_database()
    {
        $migrationDb = Mockery::mock(IMigrationDatabase::class);
        $messageFactory = new MessageFactory(
            $migrationDb,
            $this->serviceConnectorMock,
            $this->cacheMock,
            $this->cookieMock
        );

        $canMigrate = $messageFactory->canMigrate();

        $this->assertTrue($canMigrate);
    }

    public function test_can_migrate_returns_false_for_regular_database()
    {
        $canMigrate = $this->messageFactory->canMigrate();

        $this->assertFalse($canMigrate);
    }

    public function test_migrate_returns_self_and_ensures_deployment_exists_when_not_migrating()
    {
        $deployment = new LtiDeployment('test-deployment');
        $launchMessage = $this->createMockLaunchMessage();

        $launchMessage->shouldReceive('hasClaim')
            ->with(Lti1p1::class)
            ->andReturn(false);

        $result = $this->messageFactory->migrate($deployment, $launchMessage);

        $this->assertSame($this->messageFactory, $result);
    }

    public function test_migrate_throws_exception_for_missing_oauth_consumer_key_sign()
    {
        $deployment = null;
        $migrationDb = Mockery::mock(IMigrationDatabase::class);
        $messageFactory = new MessageFactory(
            $migrationDb,
            $this->serviceConnectorMock,
            $this->cacheMock,
            $this->cookieMock
        );

        $launchMessage = $this->createMockLaunchMessage();
        $launchMessage->shouldReceive('hasClaim')
            ->with(Lti1p1::class)
            ->andReturn(false);

        $migrationDb->shouldReceive('shouldMigrate')
            ->with($launchMessage)
            ->andReturn(true);

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(MessageFactory::ERR_OAUTH_KEY_SIGN_MISSING);

        $messageFactory->migrate($deployment, $launchMessage);
    }

    public function test_migrate_throws_exception_for_null_oauth_consumer_key_sign()
    {
        $deployment = null;
        $migrationDb = Mockery::mock(IMigrationDatabase::class);
        $messageFactory = new MessageFactory(
            $migrationDb,
            $this->serviceConnectorMock,
            $this->cacheMock,
            $this->cookieMock
        );

        $launchMessage = $this->createMockLaunchMessage();
        $lti1p1Claim = Mockery::mock(Lti1p1::class);

        $launchMessage->shouldReceive('hasClaim')
            ->with(Lti1p1::class)
            ->andReturn(true);
        $launchMessage->shouldReceive('ltiClaim1p1')
            ->andReturn($lti1p1Claim);

        $lti1p1Claim->shouldReceive('oauthConsumerKeySign')
            ->andReturn(null);

        $migrationDb->shouldReceive('shouldMigrate')
            ->with($launchMessage)
            ->andReturn(true);

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(MessageFactory::ERR_OAUTH_KEY_SIGN_MISSING);

        $messageFactory->migrate($deployment, $launchMessage);
    }

    public function test_migrate_throws_exception_for_unverified_oauth_key()
    {
        $deployment = null;
        $migrationDb = Mockery::mock(IMigrationDatabase::class);
        $messageFactory = new MessageFactory(
            $migrationDb,
            $this->serviceConnectorMock,
            $this->cacheMock,
            $this->cookieMock
        );

        $launchMessage = $this->createMockLaunchMessage();
        $lti1p1Claim = Mockery::mock(Lti1p1::class);

        $launchMessage->shouldReceive('hasClaim')
            ->with(Lti1p1::class)
            ->andReturn(true);
        $launchMessage->shouldReceive('ltiClaim1p1')
            ->andReturn($lti1p1Claim);

        $lti1p1Claim->shouldReceive('oauthConsumerKeySign')
            ->andReturn('test-signature');

        $migrationDb->shouldReceive('shouldMigrate')
            ->with($launchMessage)
            ->andReturn(true);
        $migrationDb->shouldReceive('findLti1p1Keys')
            ->with($launchMessage)
            ->andReturn([]);

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(MessageFactory::ERR_OAUTH_KEY_SIGN_NOT_VERIFIED);

        $messageFactory->migrate($deployment, $launchMessage);
    }

    public function test_validate_deployment_ensures_deployment_exists_for_non_migration_db()
    {
        $jwt = [
            'body' => [
                'iss' => 'https://test.issuer.com',
                'aud' => 'test-client-id',
                DeploymentId::claimKey() => 'test-deployment',
            ],
        ];

        $this->databaseMock->shouldReceive('findDeployment')
            ->with('https://test.issuer.com', 'test-deployment', 'test-client-id')
            ->andReturn(null);

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(MessageFactory::ERR_NO_DEPLOYMENT);

        $this->invokeMethod($this->messageFactory, 'validateDeployment', [$jwt]);
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

    private function createMockLaunchMessage()
    {
        return Mockery::mock(LaunchMessage::class);
    }

    private function invokeMethod($object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
