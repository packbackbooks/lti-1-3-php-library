<?php

namespace Tests;

use Carbon\Carbon;
use Firebase\JWT\JWT;
use GuzzleHttp\Psr7\Response;
use Mockery;
use Mockery\MockInterface;
use Packback\Lti1p3\Interfaces\ICache;
use Packback\Lti1p3\Interfaces\ICookie;
use Packback\Lti1p3\Interfaces\IDatabase;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\Interfaces\IMigrationDatabase;
use Packback\Lti1p3\JwksEndpoint;
use Packback\Lti1p3\Lti1p1Key;
use Packback\Lti1p3\LtiAssignmentsGradesService;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\LtiCourseGroupsService;
use Packback\Lti1p3\LtiDeepLink;
use Packback\Lti1p3\LtiDeployment;
use Packback\Lti1p3\LtiException;
use Packback\Lti1p3\LtiMessageLaunch;
use Packback\Lti1p3\LtiNamesRolesProvisioningService;

class LtiMessageLaunchTest extends TestCase
{
    public const ISSUER_URL = 'https://ltiadvantagevalidator.imsglobal.org';
    public const JWKS_FILE = '/tmp/jwks.json';
    public const CERT_DATA_DIR = __DIR__.'/data/certification/';
    public const PRIVATE_KEY = __DIR__.'/data/private.key';
    public const STATE = 'state';

    protected function setUp(): void
    {
        $this->cache = Mockery::mock(ICache::class);
        $this->cookie = Mockery::mock(ICookie::class);
        $this->database = Mockery::mock(IDatabase::class);
        $this->migrationDatabase = Mockery::mock(IDatabase::class, IMigrationDatabase::class);
        $this->serviceConnector = Mockery::mock(ILtiServiceConnector::class);
        $this->registration = Mockery::mock(ILtiRegistration::class);
        $this->deployment = Mockery::mock(LtiDeployment::class);

        $this->messageLaunch = new LtiMessageLaunch(
            $this->database,
            $this->cache,
            $this->cookie,
            $this->serviceConnector
        );

        $this->issuer = [
            'id' => 'issuer_id',
            'issuer' => static::ISSUER_URL,
            'client_id' => 'imstester_3dfad6d',
            'auth_login_url' => 'https://ltiadvantagevalidator.imsglobal.org/ltitool/oidcauthurl.html',
            'auth_token_url' => 'https://ltiadvantagevalidator.imsglobal.org/ltitool/authcodejwt.html',
            'alg' => 'RS256',
            'key_set_url' => static::JWKS_FILE,
            'kid' => 'key-id',
            'tool_private_key' => file_get_contents(static::PRIVATE_KEY),
        ];

        $this->key = [
            'version' => LtiConstants::V1_3,
            'issuer_id' => $this->issuer['id'],
            'deployment_id' => 'testdeploy',
            'campus_id' => 1,
        ];

        $this->payload = [
            LtiConstants::MESSAGE_TYPE => 'LtiResourceLinkRequest',
            LtiConstants::VERSION => LtiConstants::V1_3,
            LtiConstants::RESOURCE_LINK => [
                'id' => 'd3a2504bba5184799a38f141e8df2335cfa8206d',
                'description' => null,
                'title' => null,
                'validation_context' => null,
                'errors' => [
                    'errors' => [],
                ],
            ],
            'aud' => $this->issuer['client_id'],
            'azp' => $this->issuer['client_id'],
            LtiConstants::DEPLOYMENT_ID => $this->key['deployment_id'],
            'exp' => Carbon::now()->addDay()->timestamp,
            'iat' => Carbon::now()->subDay()->timestamp,
            'iss' => $this->issuer['issuer'],
            'nonce' => 'nonce-5e73ef2f4c6ea0.93530902',
            'sub' => '66b6a854-9f43-4bb2-90e8-6653c9126272',
            LtiConstants::TARGET_LINK_URI => 'https://lms-api.packback.localhost/api/lti/launch',
            LtiConstants::CONTEXT => [
                'id' => 'd3a2504bba5184799a38f141e8df2335cfa8206d',
                'label' => 'Canvas Unlauched',
                'title' => 'Canvas - A Fresh Course That Remains Unlaunched',
                'type' => [
                    LtiConstants::COURSE_OFFERING,
                ],
                'validation_context' => null,
                'errors' => [
                    'errors' => [],
                ],
            ],
            LtiConstants::TOOL_PLATFORM => [
                'guid' => 'FnwyPrXqSxwv8QCm11UwILpDJMAUPJ9WGn8zcvBM:canvas-lms',
                'name' => 'Packback Engineering',
                'version' => 'cloud',
                'product_family_code' => 'canvas',
                'validation_context' => null,
                'errors' => [
                    'errors' => [],
                ],
            ],
            LtiConstants::LAUNCH_PRESENTATION => [
                'document_target' => 'iframe',
                'height' => 400,
                'width' => 800,
                'return_url' => 'https://canvas.localhost/courses/3/external_content/success/external_tool_redirect',
                'locale' => 'en',
                'validation_context' => null,
                'errors' => [
                    'errors' => [],
                ],
            ],
            'locale' => 'en',
            LtiConstants::ROLES => [
                LtiConstants::INSTITUTION_ADMINISTRATOR,
                LtiConstants::INSTITUTION_INSTRUCTOR,
                LtiConstants::MEMBERSHIP_INSTRUCTOR,
                LtiConstants::SYSTEM_SYSADMIN,
                LtiConstants::SYSTEM_USER,
            ],
            LtiConstants::CUSTOM => [],
            'errors' => [
                'errors' => [],
            ],
        ];
    }
    private LtiMessageLaunch $messageLaunch;
    private MockInterface $cache;
    private MockInterface $cookie;
    private MockInterface $database;
    private MockInterface $serviceConnector;
    private MockInterface $registration;
    private array $issuer;
    private array $key;
    private array $payload;
    private $migrationDatabase;
    private $deployment;

    public function test_it_instantiates()
    {
        $this->assertInstanceOf(LtiMessageLaunch::class, $this->messageLaunch);
    }

    public function test_it_creates_a_new_instance()
    {
        $messageLaunch = LtiMessageLaunch::new(
            $this->database,
            $this->cache,
            $this->cookie,
            $this->serviceConnector
        );

        $this->assertInstanceOf(LtiMessageLaunch::class, $messageLaunch);
    }

    public function test_it_gets_a_launch_from_the_cache()
    {
        $this->cache->shouldReceive('getLaunchData')
            ->once()->andReturn($this->payload);
        $this->database->shouldReceive('findRegistrationByIssuer')
            ->once()->andReturn($this->registration);
        $this->registration->shouldReceive('getClientId')
            ->once()->andReturn($this->issuer['client_id']);

        $actual = $this->messageLaunch::fromCache('id_token', $this->database, $this->cache,
            $this->cookie,
            $this->serviceConnector);

        $this->assertInstanceOf(LtiMessageLaunch::class, $actual);
    }

    public function test_it_validates_a_launch()
    {
        $params = [
            'utf8' => '✓',
            'id_token' => $this->buildJWT($this->payload, $this->issuer),
            'state' => static::STATE,
        ];

        $this->cookie->shouldReceive('getCookie')
            ->once()->andReturn($params['state']);
        $this->cache->shouldReceive('checkNonceIsValid')
            ->once()->andReturn(true);
        $this->database->shouldReceive('findRegistrationByIssuer')
            ->once()->andReturn($this->registration);
        $this->registration->shouldReceive('getClientId')
            ->once()->andReturn($this->issuer['client_id']);
        $this->registration->shouldReceive('getKeySetUrl')
            ->once()->andReturn($this->issuer['key_set_url']);
        $this->serviceConnector->shouldReceive('makeRequest')
            ->once()->andReturn(Mockery::mock(Response::class));
        $this->serviceConnector->shouldReceive('getResponseBody')
            ->once()->andReturn(json_decode(file_get_contents(static::JWKS_FILE), true));
        $this->database->shouldReceive('findDeployment')
            ->once()->andReturn(new LtiDeployment('a deployment'));

        $this->messageLaunch->setRequest($params);

        $actual = $this->messageLaunch->validate();

        $this->assertInstanceOf(LtiMessageLaunch::class, $actual);
    }

    public function test_a_launch_fails_if_cookies_are_disabled()
    {
        $payload = [
            'utf8' => '✓',
            'id_token' => $this->buildJWT($this->payload, $this->issuer),
            'state' => static::STATE,
        ];
        $this->cookie->shouldReceive('getCookie')
            ->once()->andReturn();

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(LtiMessageLaunch::ERR_STATE_NOT_FOUND);

        $this->messageLaunch->setRequest($payload);

        $actual = $this->messageLaunch->validate();
    }

    public function test_a_launch_fails_if_id_token_is_missing()
    {
        $payload = [
            'utf8' => '✓',
            'state' => static::STATE,
        ];
        $this->cookie->shouldReceive('getCookie')
            ->once()->andReturn($payload['state']);

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(LtiMessageLaunch::ERR_MISSING_ID_TOKEN);

        $this->messageLaunch->setRequest($payload);

        $actual = $this->messageLaunch->validate();
    }

    public function test_a_launch_fails_if_jwt_is_invalid()
    {
        $payload = [
            'utf8' => '✓',
            'id_token' => 'nope',
            'state' => static::STATE,
        ];
        $this->cookie->shouldReceive('getCookie')
            ->once()->andReturn($payload['state']);

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(LtiMessageLaunch::ERR_INVALID_ID_TOKEN);

        $this->messageLaunch->setRequest($payload);

        $actual = $this->messageLaunch->validate();
    }

    public function test_a_launch_fails_if_nonce_is_missing()
    {
        $jwtPayload = $this->payload;
        unset($jwtPayload['nonce']);
        $payload = [
            'utf8' => '✓',
            'id_token' => $this->buildJWT($jwtPayload, $this->issuer),
            'state' => static::STATE,
        ];
        $this->cookie->shouldReceive('getCookie')
            ->once()->andReturn($payload['state']);

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(LtiMessageLaunch::ERR_MISSING_NONCE);

        $this->messageLaunch->setRequest($payload);

        $actual = $this->messageLaunch->validate();
    }

    public function test_a_launch_fails_if_nonce_is_invalid()
    {
        $jwtPayload = $this->payload;
        $jwtPayload['nonce'] = 'schmonze';
        $payload = [
            'utf8' => '✓',
            'id_token' => $this->buildJWT($jwtPayload, $this->issuer),
            'state' => static::STATE,
        ];
        $this->cookie->shouldReceive('getCookie')
            ->once()->andReturn($payload['state']);
        $this->cache->shouldReceive('checkNonceIsValid')
            ->once()->andReturn(false);

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(LtiMessageLaunch::ERR_INVALID_NONCE);

        $this->messageLaunch->setRequest($payload);

        $actual = $this->messageLaunch->validate();
    }

    public function test_a_launch_fails_if_missing_registration()
    {
        $payload = [
            'utf8' => '✓',
            'id_token' => $this->buildJWT($this->payload, $this->issuer),
            'state' => static::STATE,
        ];
        $this->cookie->shouldReceive('getCookie')
            ->once()->andReturn($payload['state']);
        $this->cache->shouldReceive('checkNonceIsValid')
            ->once()->andReturn(true);
        $this->database->shouldReceive('findRegistrationByIssuer')
            ->once()->andReturn();

        $this->expectException(LtiException::class);
        $expectedMsg = $this->messageLaunch->getMissingRegistrationErrorMsg($this->issuer['issuer'], $this->issuer['client_id']);
        $this->expectExceptionMessage($expectedMsg);

        $this->messageLaunch->setRequest($payload);

        $actual = $this->messageLaunch->validate();
    }

    public function test_a_launch_fails_if_registration_client_id_is_wrong()
    {
        $payload = [
            'utf8' => '✓',
            'id_token' => $this->buildJWT($this->payload, $this->issuer),
            'state' => static::STATE,
        ];
        $this->cookie->shouldReceive('getCookie')
            ->once()->andReturn($payload['state']);
        $this->cache->shouldReceive('checkNonceIsValid')
            ->once()->andReturn(true);
        $this->database->shouldReceive('findRegistrationByIssuer')
            ->once()->andReturn($this->registration);
        $this->registration->shouldReceive('getClientId')
            ->once()->andReturn('nope');

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(LtiMessageLaunch::ERR_CLIENT_NOT_REGISTERED);

        $this->messageLaunch->setRequest($payload);

        $actual = $this->messageLaunch->validate();
    }

    public function test_a_launch_fails_if_kid_is_missing()
    {
        $jwtHeader = $this->issuer;
        unset($jwtHeader['kid']);
        $payload = [
            'utf8' => '✓',
            'id_token' => $this->buildJWT($this->payload, $jwtHeader),
            'state' => static::STATE,
        ];
        $this->cookie->shouldReceive('getCookie')
            ->once()->andReturn($payload['state']);
        $this->cache->shouldReceive('checkNonceIsValid')
            ->once()->andReturn(true);
        $this->database->shouldReceive('findRegistrationByIssuer')
            ->once()->andReturn($this->registration);
        $this->registration->shouldReceive('getClientId')
            ->once()->andReturn($this->payload['aud']);

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(LtiMessageLaunch::ERR_NO_KID);

        $this->messageLaunch->setRequest($payload);

        $actual = $this->messageLaunch->validate();
    }

    public function test_a_launch_fails_if_no_public_keys_are_returned()
    {
        $params = [
            'utf8' => '✓',
            'id_token' => $this->buildJWT($this->payload, $this->issuer),
            'state' => static::STATE,
        ];

        $this->cookie->shouldReceive('getCookie')
            ->once()->andReturn($params['state']);
        $this->cache->shouldReceive('checkNonceIsValid')
            ->once()->andReturn(true);
        $this->database->shouldReceive('findRegistrationByIssuer')
            ->once()->andReturn($this->registration);
        $this->registration->shouldReceive('getClientId')
            ->once()->andReturn($this->issuer['client_id']);
        $this->registration->shouldReceive('getKeySetUrl')
            ->once()->andReturn($this->issuer['key_set_url']);
        $this->serviceConnector->shouldReceive('makeRequest')
            ->once()->andReturn(Mockery::mock(Response::class));
        $this->serviceConnector->shouldReceive('getResponseBody')
            ->once()->andReturn([]);

        $this->messageLaunch->setRequest($params);

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(LtiMessageLaunch::ERR_FETCH_PUBLIC_KEY);

        $this->messageLaunch->validate();
    }

    public function test_a_launch_fails_if_no_public_keys_match()
    {
        $params = [
            'utf8' => '✓',
            'id_token' => $this->buildJWT($this->payload, $this->issuer),
            'state' => static::STATE,
        ];

        $this->cookie->shouldReceive('getCookie')
            ->once()->andReturn($params['state']);
        $this->cache->shouldReceive('checkNonceIsValid')
            ->once()->andReturn(true);
        $this->database->shouldReceive('findRegistrationByIssuer')
            ->once()->andReturn($this->registration);
        $this->registration->shouldReceive('getClientId')
            ->once()->andReturn($this->issuer['client_id']);
        $this->registration->shouldReceive('getKeySetUrl')
            ->once()->andReturn($this->issuer['key_set_url']);
        $this->serviceConnector->shouldReceive('makeRequest')
            ->once()->andReturn(Mockery::mock(Response::class));
        $this->serviceConnector->shouldReceive('getResponseBody')
            ->once()->andReturn([
                'keys' => [[
                    'kid' => 'not mine',
                ]],
            ]);

        $this->messageLaunch->setRequest($params);

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(LtiMessageLaunch::ERR_NO_MATCHING_PUBLIC_KEY);

        $this->messageLaunch->validate();
    }

    public function test_a_launch_fails_if_key_algorithm_doesnt_match()
    {
        $params = [
            'utf8' => '✓',
            'id_token' => $this->buildJWT($this->payload, $this->issuer),
            'state' => static::STATE,
        ];

        $this->cookie->shouldReceive('getCookie')
            ->once()->andReturn($params['state']);
        $this->cache->shouldReceive('checkNonceIsValid')
            ->once()->andReturn(true);
        $this->database->shouldReceive('findRegistrationByIssuer')
            ->once()->andReturn($this->registration);
        $this->registration->shouldReceive('getClientId')
            ->once()->andReturn($this->issuer['client_id']);
        $this->registration->shouldReceive('getKeySetUrl')
            ->once()->andReturn($this->issuer['key_set_url']);
        $this->serviceConnector->shouldReceive('makeRequest')
            ->once()->andReturn(Mockery::mock(Response::class));
        $this->serviceConnector->shouldReceive('getResponseBody')
            ->once()->andReturn([
                'keys' => [[
                    'kid' => $this->issuer['kid'],
                    'kty' => 'kitty',
                ]],
            ]);

        $this->messageLaunch->setRequest($params);

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(LtiMessageLaunch::ERR_MISMATCHED_ALG_KEY);

        $this->messageLaunch->validate();
    }

    public function test_a_launch_fails_if_deployment_id_is_missing()
    {
        $jwtPayload = $this->payload;
        unset($jwtPayload[LtiConstants::DEPLOYMENT_ID]);
        $payload = [
            'utf8' => '✓',
            'id_token' => $this->buildJWT($jwtPayload, $this->issuer),
            'state' => static::STATE,
        ];
        $this->cookie->shouldReceive('getCookie')
            ->once()->andReturn($payload['state']);
        $this->cache->shouldReceive('checkNonceIsValid')
            ->once()->andReturn(true);
        $this->database->shouldReceive('findRegistrationByIssuer')
            ->once()->andReturn($this->registration);
        $this->registration->shouldReceive('getClientId')
            ->once()->andReturn($this->payload['aud']);
        $this->registration->shouldReceive('getKeySetUrl')
            ->once()->andReturn($this->issuer['key_set_url']);
        $this->serviceConnector->shouldReceive('makeRequest')
            ->once()->andReturn(Mockery::mock(Response::class));
        $this->serviceConnector->shouldReceive('getResponseBody')
            ->once()->andReturn(json_decode(file_get_contents(static::JWKS_FILE), true));

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(LtiMessageLaunch::ERR_MISSING_DEPLOYEMENT_ID);

        $this->messageLaunch->setRequest($payload);

        $actual = $this->messageLaunch->validate();
    }

    public function test_a_launch_fails_if_no_deployment()
    {
        $jwtPayload = $this->payload;
        $payload = [
            'utf8' => '✓',
            'id_token' => $this->buildJWT($jwtPayload, $this->issuer),
            'state' => static::STATE,
        ];
        $this->cookie->shouldReceive('getCookie')
            ->once()->andReturn($payload['state']);
        $this->cache->shouldReceive('checkNonceIsValid')
            ->once()->andReturn(true);
        $this->database->shouldReceive('findRegistrationByIssuer')
            ->once()->andReturn($this->registration);
        $this->registration->shouldReceive('getClientId')
            ->once()->andReturn($this->payload['aud']);
        $this->registration->shouldReceive('getKeySetUrl')
            ->once()->andReturn($this->issuer['key_set_url']);
        $this->serviceConnector->shouldReceive('makeRequest')
            ->once()->andReturn(Mockery::mock(Response::class));
        $this->serviceConnector->shouldReceive('getResponseBody')
            ->once()->andReturn(json_decode(file_get_contents(static::JWKS_FILE), true));
        $this->database->shouldReceive('findDeployment')
            ->once()->andReturn();

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(LtiMessageLaunch::ERR_NO_DEPLOYMENT);

        $this->messageLaunch->setRequest($payload);

        $actual = $this->messageLaunch->validate();
    }

    public function test_a_launch_fails_if_the_payload_is_invalid()
    {
        $payload = $this->payload;
        unset($payload[LtiConstants::MESSAGE_TYPE]);
        $params = [
            'utf8' => '✓',
            'id_token' => $this->buildJWT($payload, $this->issuer),
            'state' => static::STATE,
        ];

        $this->cookie->shouldReceive('getCookie')
            ->once()->andReturn($params['state']);
        $this->cache->shouldReceive('checkNonceIsValid')
            ->once()->andReturn(true);
        $this->database->shouldReceive('findRegistrationByIssuer')
            ->once()->andReturn($this->registration);
        $this->registration->shouldReceive('getClientId')
            ->once()->andReturn($this->issuer['client_id']);
        $this->registration->shouldReceive('getKeySetUrl')
            ->once()->andReturn($this->issuer['key_set_url']);
        $this->serviceConnector->shouldReceive('makeRequest')
            ->once()->andReturn(Mockery::mock(Response::class));
        $this->serviceConnector->shouldReceive('getResponseBody')
            ->once()->andReturn(json_decode(file_get_contents(static::JWKS_FILE), true));
        $this->database->shouldReceive('findDeployment')
            ->once()->andReturn(new LtiDeployment('a deployment'));
        $this->messageLaunch->setRequest($params);

        $this->expectException(LtiException::class);

        $this->messageLaunch->validate();
    }

    public function test_it_initializes_a_launch()
    {
        $messageLaunch = new LtiMessageLaunch(
            $this->migrationDatabase,
            $this->cache,
            $this->cookie,
            $this->serviceConnector
        );

        $payload = $this->payload;
        $payload[LtiConstants::LTI1P1]['oauth_consumer_key_sign'] = 'foo';

        $params = [
            'utf8' => '✓',
            'id_token' => $this->buildJWT($payload, $this->issuer),
            'state' => static::STATE,
        ];

        $matchingKey = Mockery::mock(Lti1p1Key::class);

        $this->cookie->shouldReceive('getCookie')
            ->once()->andReturn($params['state']);
        $this->cache->shouldReceive('checkNonceIsValid')
            ->once()->andReturn(true);
        $this->migrationDatabase->shouldReceive('findRegistrationByIssuer')
            ->once()->andReturn($this->registration);
        $this->registration->shouldReceive('getClientId')
            ->once()->andReturn($this->issuer['client_id']);
        $this->registration->shouldReceive('getKeySetUrl')
            ->once()->andReturn($this->issuer['key_set_url']);
        $this->serviceConnector->shouldReceive('makeRequest')
            ->once()->andReturn(Mockery::mock(Response::class));
        $this->serviceConnector->shouldReceive('getResponseBody')
            ->once()->andReturn(json_decode(file_get_contents(static::JWKS_FILE), true));
        $this->migrationDatabase->shouldReceive('findDeployment')
            ->once()->andReturn(null);
        $this->migrationDatabase->shouldReceive('shouldMigrate')
            ->once()->andReturn(true);
        $this->migrationDatabase->shouldReceive('findLti1p1Keys')
            ->once()->andReturn([$matchingKey]);
        $matchingKey->shouldReceive('sign')
            ->once()->andReturn($payload[LtiConstants::LTI1P1]['oauth_consumer_key_sign']);
        $this->migrationDatabase->shouldReceive('migrateFromLti1p1')
            ->once()->andReturn($this->deployment);
        $this->cache->shouldReceive('cacheLaunchData')
            ->once()->andReturn(true);

        $actual = $messageLaunch->initialize($params);

        $this->assertInstanceOf(LtiMessageLaunch::class, $actual);
    }

    public function test_it_fails_to_initialize_if_oauth_consumer_key_sign_is_missing()
    {
        $messageLaunch = new LtiMessageLaunch(
            $this->migrationDatabase,
            $this->cache,
            $this->cookie,
            $this->serviceConnector
        );

        $payload = $this->payload;

        $params = [
            'utf8' => '✓',
            'id_token' => $this->buildJWT($payload, $this->issuer),
            'state' => static::STATE,
        ];

        $this->cookie->shouldReceive('getCookie')
            ->once()->andReturn($params['state']);
        $this->cache->shouldReceive('checkNonceIsValid')
            ->once()->andReturn(true);
        $this->migrationDatabase->shouldReceive('findRegistrationByIssuer')
            ->once()->andReturn($this->registration);
        $this->registration->shouldReceive('getClientId')
            ->once()->andReturn($this->issuer['client_id']);
        $this->registration->shouldReceive('getKeySetUrl')
            ->once()->andReturn($this->issuer['key_set_url']);
        $this->serviceConnector->shouldReceive('makeRequest')
            ->once()->andReturn(Mockery::mock(Response::class));
        $this->serviceConnector->shouldReceive('getResponseBody')
            ->once()->andReturn(json_decode(file_get_contents(static::JWKS_FILE), true));
        $this->migrationDatabase->shouldReceive('findDeployment')
            ->once()->andReturn(null);
        $this->migrationDatabase->shouldReceive('shouldMigrate')
            ->once()->andReturn(true);

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(LtiMessageLaunch::ERR_OAUTH_KEY_SIGN_MISSING);

        $actual = $messageLaunch->initialize($params);
    }

    public function test_it_fails_to_initialize_if_no_matching_key_is_found()
    {
        $messageLaunch = new LtiMessageLaunch(
            $this->migrationDatabase,
            $this->cache,
            $this->cookie,
            $this->serviceConnector
        );

        $payload = $this->payload;
        $payload[LtiConstants::LTI1P1]['oauth_consumer_key_sign'] = 'foo';

        $params = [
            'utf8' => '✓',
            'id_token' => $this->buildJWT($payload, $this->issuer),
            'state' => static::STATE,
        ];

        $matchingKey = Mockery::mock(Lti1p1Key::class);

        $this->cookie->shouldReceive('getCookie')
            ->once()->andReturn($params['state']);
        $this->cache->shouldReceive('checkNonceIsValid')
            ->once()->andReturn(true);
        $this->migrationDatabase->shouldReceive('findRegistrationByIssuer')
            ->once()->andReturn($this->registration);
        $this->registration->shouldReceive('getClientId')
            ->once()->andReturn($this->issuer['client_id']);
        $this->registration->shouldReceive('getKeySetUrl')
            ->once()->andReturn($this->issuer['key_set_url']);
        $this->serviceConnector->shouldReceive('makeRequest')
            ->once()->andReturn(Mockery::mock(Response::class));
        $this->serviceConnector->shouldReceive('getResponseBody')
            ->once()->andReturn(json_decode(file_get_contents(static::JWKS_FILE), true));
        $this->migrationDatabase->shouldReceive('findDeployment')
            ->once()->andReturn(null);
        $this->migrationDatabase->shouldReceive('shouldMigrate')
            ->once()->andReturn(true);
        $this->migrationDatabase->shouldReceive('findLti1p1Keys')
            ->once()->andReturn([$matchingKey]);
        $matchingKey->shouldReceive('sign')
            ->once()->andReturn('bar');

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(LtiMessageLaunch::ERR_OAUTH_KEY_SIGN_NOT_VERIFIED);

        $actual = $messageLaunch->initialize($params);
    }

    public function test_it_fails_to_initialize_if_deployment_is_not_returned()
    {
        $messageLaunch = new LtiMessageLaunch(
            $this->migrationDatabase,
            $this->cache,
            $this->cookie,
            $this->serviceConnector
        );

        $payload = $this->payload;
        $payload[LtiConstants::LTI1P1]['oauth_consumer_key_sign'] = 'foo';

        $params = [
            'utf8' => '✓',
            'id_token' => $this->buildJWT($payload, $this->issuer),
            'state' => static::STATE,
        ];

        $matchingKey = Mockery::mock(Lti1p1Key::class);

        $this->cookie->shouldReceive('getCookie')
            ->once()->andReturn($params['state']);
        $this->cache->shouldReceive('checkNonceIsValid')
            ->once()->andReturn(true);
        $this->migrationDatabase->shouldReceive('findRegistrationByIssuer')
            ->once()->andReturn($this->registration);
        $this->registration->shouldReceive('getClientId')
            ->once()->andReturn($this->issuer['client_id']);
        $this->registration->shouldReceive('getKeySetUrl')
            ->once()->andReturn($this->issuer['key_set_url']);
        $this->serviceConnector->shouldReceive('makeRequest')
            ->once()->andReturn(Mockery::mock(Response::class));
        $this->serviceConnector->shouldReceive('getResponseBody')
            ->once()->andReturn(json_decode(file_get_contents(static::JWKS_FILE), true));
        $this->migrationDatabase->shouldReceive('findDeployment')
            ->once()->andReturn(null);
        $this->migrationDatabase->shouldReceive('shouldMigrate')
            ->once()->andReturn(true);
        $this->migrationDatabase->shouldReceive('findLti1p1Keys')
            ->once()->andReturn([$matchingKey]);
        $matchingKey->shouldReceive('sign')
            ->once()->andReturn($payload[LtiConstants::LTI1P1]['oauth_consumer_key_sign']);
        $this->migrationDatabase->shouldReceive('migrateFromLti1p1')
            ->once()->andReturn(null);

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(LtiMessageLaunch::ERR_NO_DEPLOYMENT);

        $actual = $messageLaunch->initialize($params);
    }

    public function test_a_launch_has_nrps()
    {
        $payload = $this->payload;
        $payload[LtiConstants::NRPS_CLAIM_SERVICE]['context_memberships_url'] = 'https://example.com';
        $launch = $this->fakeLaunch($payload);

        $actual = $launch->hasNrps();

        $this->assertTrue($actual);
    }

    public function test_a_launch_does_not_have_nrps()
    {
        $payload = $this->payload;
        unset($payload[LtiConstants::NRPS_CLAIM_SERVICE]['context_memberships_url']);
        $launch = $this->fakeLaunch($payload);

        $actual = $launch->hasNrps();

        $this->assertFalse($actual);
    }

    public function test_get_nrps_for_a_launch()
    {
        $payload = $this->payload;
        $payload[LtiConstants::NRPS_CLAIM_SERVICE]['context_memberships_url'] = 'https://example.com';
        $launch = $this->fakeLaunch($payload);

        $actual = $launch->getNrps();

        $this->assertInstanceOf(LtiNamesRolesProvisioningService::class, $actual);
    }

    public function test_a_launch_has_gs()
    {
        $payload = $this->payload;
        $payload[LtiConstants::GS_CLAIM_SERVICE]['context_groups_url'] = 'https://example.com';
        $launch = $this->fakeLaunch($payload);

        $actual = $launch->hasGs();

        $this->assertTrue($actual);
    }

    public function test_a_launch_does_not_have_gs()
    {
        $payload = $this->payload;
        unset($payload[LtiConstants::GS_CLAIM_SERVICE]['context_groups_url']);
        $launch = $this->fakeLaunch($payload);

        $actual = $launch->hasGs();

        $this->assertFalse($actual);
    }

    public function test_get_gs_for_a_launch()
    {
        $payload = $this->payload;
        $payload[LtiConstants::GS_CLAIM_SERVICE]['context_groups_url'] = 'https://example.com';
        $launch = $this->fakeLaunch($payload);

        $actual = $launch->getGs();

        $this->assertInstanceOf(LtiCourseGroupsService::class, $actual);
    }

    public function test_a_launch_has_ags()
    {
        $payload = $this->payload;
        $payload[LtiConstants::AGS_CLAIM_ENDPOINT] = ['https://example.com'];
        $launch = $this->fakeLaunch($payload);

        $actual = $launch->hasAgs();

        $this->assertTrue($actual);
    }

    public function test_a_launch_does_not_have_ags()
    {
        $payload = $this->payload;
        unset($payload[LtiConstants::AGS_CLAIM_ENDPOINT]);
        $launch = $this->fakeLaunch($payload);

        $actual = $launch->hasAgs();

        $this->assertFalse($actual);
    }

    public function test_get_ags_for_a_launch()
    {
        $payload = $this->payload;
        $payload[LtiConstants::AGS_CLAIM_ENDPOINT] = ['https://example.com'];
        $launch = $this->fakeLaunch($payload);

        $actual = $launch->getAgs();

        $this->assertInstanceOf(LtiAssignmentsGradesService::class, $actual);
    }

    public function test_a_launch_is_a_deep_link()
    {
        $payload = $this->payload;
        $payload[LtiConstants::MESSAGE_TYPE] = LtiMessageLaunch::TYPE_DEEPLINK;
        $launch = $this->fakeLaunch($payload);

        $actual = $launch->isDeepLinkLaunch();

        $this->assertTrue($actual);
    }

    public function test_a_launch_is_not_a_deep_link()
    {
        $payload = $this->payload;
        $payload[LtiConstants::MESSAGE_TYPE] = LtiMessageLaunch::TYPE_SUBMISSIONREVIEW;
        $launch = $this->fakeLaunch($payload);

        $actual = $launch->isDeepLinkLaunch();

        $this->assertFalse($actual);
    }

    public function test_get_deep_link_for_a_launch()
    {
        $payload = $this->payload;
        $payload[LtiConstants::DEPLOYMENT_ID] = 'deployment_id';
        $payload[LtiConstants::DL_DEEP_LINK_SETTINGS] = [];
        $launch = $this->fakeLaunch($payload);

        $actual = $launch->getDeepLink();

        $this->assertInstanceOf(LtiDeepLink::class, $actual);
    }

    public function test_a_launch_is_a_submission_review()
    {
        $payload = $this->payload;
        $payload[LtiConstants::MESSAGE_TYPE] = LtiMessageLaunch::TYPE_SUBMISSIONREVIEW;
        $launch = $this->fakeLaunch($payload);

        $actual = $launch->isSubmissionReviewLaunch();

        $this->assertTrue($actual);
    }

    public function test_a_launch_is_not_a_submission_review()
    {
        $payload = $this->payload;
        $payload[LtiConstants::MESSAGE_TYPE] = LtiMessageLaunch::TYPE_DEEPLINK;
        $launch = $this->fakeLaunch($payload);

        $actual = $launch->isSubmissionReviewLaunch();

        $this->assertFalse($actual);
    }

    public function test_a_launch_is_a_resource_link()
    {
        $payload = $this->payload;
        $payload[LtiConstants::MESSAGE_TYPE] = LtiMessageLaunch::TYPE_RESOURCELINK;
        $launch = $this->fakeLaunch($payload);

        $actual = $launch->isResourceLaunch();

        $this->assertTrue($actual);
    }

    public function test_a_launch_is_not_a_resource()
    {
        $payload = $this->payload;
        $payload[LtiConstants::MESSAGE_TYPE] = LtiMessageLaunch::TYPE_DEEPLINK;
        $launch = $this->fakeLaunch($payload);

        $actual = $launch->isResourceLaunch();

        $this->assertFalse($actual);
    }

    public function test_get_launch_data()
    {
        $payload = $this->payload;
        $payload[LtiConstants::MESSAGE_TYPE] = LtiMessageLaunch::TYPE_DEEPLINK;
        $launch = $this->fakeLaunch($payload);

        $actual = $launch->getLaunchData();

        $this->assertEquals($payload, $actual);
    }

    public function test_get_launch_id()
    {
        $expected = 'launch_id';

        $payload = $this->payload;
        $payload[LtiConstants::MESSAGE_TYPE] = LtiMessageLaunch::TYPE_DEEPLINK;
        $launch = $this->fakeLaunch($payload, $expected);

        $actual = $launch->getLaunchId();

        $this->assertEquals($expected, $actual);
    }

    public function tesGetLaunchDataForALaunch()
    {
        $launch = $this->fakeLaunch($this->payload);

        $actual = $launch->getLaunchData();

        $this->assertEquals($this->payload, $actual);
    }

    private function fakeLaunch($payload, $launchId = 'id_token')
    {
        $this->cache->shouldReceive('getLaunchData')
            ->once()->andReturn($payload);
        $this->database->shouldReceive('findRegistrationByIssuer')
            ->once()->andReturn($this->registration);
        $this->registration->shouldReceive('getClientId')
            ->once()->andReturn($this->issuer['client_id']);

        return $this->messageLaunch::fromCache($launchId, $this->database, $this->cache, $this->cookie, $this->serviceConnector);
    }

    private function buildJWT($data, $header)
    {
        $jwks = json_encode(JwksEndpoint::new([
            $this->issuer['kid'] => $this->issuer['tool_private_key'],
        ])->getPublicJwks());
        file_put_contents(static::JWKS_FILE, $jwks);

        // If we pass in a header, use that instead of creating one automatically based on params given
        if ($header) {
            $segments = [];
            $segments[] = JWT::urlsafeB64Encode(JWT::jsonEncode($header));
            $segments[] = JWT::urlsafeB64Encode(JWT::jsonEncode($data));
            $signing_input = \implode('.', $segments);

            $signature = JWT::sign($signing_input, $this->issuer['tool_private_key'], $this->issuer['alg']);
            $segments[] = JWT::urlsafeB64Encode($signature);

            return \implode('.', $segments);
        }

        return JWT::encode($data, $this->issuer['tool_private_key'], $this->issuer['alg'], $this->issuer['kid']);
    }
}
