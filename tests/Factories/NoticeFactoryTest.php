<?php

namespace Tests\Factories;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\DeploymentId;
use Packback\Lti1p3\Claims\Roles;
use Packback\Lti1p3\Claims\Version;
use Packback\Lti1p3\Factories\NoticeFactory;
use Packback\Lti1p3\Interfaces\IDatabase;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\LtiException;
use Packback\Lti1p3\Messages\HelloWorldNotice;
use Tests\TestCase;

class NoticeFactoryTest extends TestCase
{
    private NoticeFactory $noticeFactory;
    private $databaseMock;
    private $serviceConnectorMock;
    private $registrationMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->databaseMock = Mockery::mock(IDatabase::class);
        $this->serviceConnectorMock = Mockery::mock(ILtiServiceConnector::class);
        $this->registrationMock = Mockery::mock(ILtiRegistration::class);

        $this->noticeFactory = new NoticeFactory(
            $this->databaseMock,
            $this->serviceConnectorMock
        );
    }

    public function test_it_creates_new_instance()
    {
        $this->assertInstanceOf(NoticeFactory::class, $this->noticeFactory);
    }

    public function test_get_type_claim_returns_notice()
    {
        $typeClaim = NoticeFactory::getTypeClaim();

        $this->assertEquals(Claim::NOTICE, $typeClaim);
    }

    public function test_get_token_key_returns_jwt()
    {
        $tokenKey = $this->invokeMethod($this->noticeFactory, 'getTokenKey');

        $this->assertEquals('jwt', $tokenKey);
    }

    public function test_get_type_name_returns_notice_type_from_jwt()
    {
        $jwt = [
            'body' => [
                Claim::NOTICE => ['type' => LtiConstants::NOTICE_TYPE_HELLOWORLD],
            ],
        ];

        $typeName = $this->noticeFactory->getTypeName($jwt);

        $this->assertEquals(LtiConstants::NOTICE_TYPE_HELLOWORLD, $typeName);
    }

    public function test_validate_jwt_format_throws_exception_for_missing_jwt()
    {
        $message = ['no_jwt' => 'value'];

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(NoticeFactory::ERR_MISSING_ID_TOKEN);

        $this->noticeFactory->validate($message);
    }

    public function test_validate_jwt_format_throws_exception_for_invalid_jwt_parts()
    {
        $message = ['jwt' => 'invalid.jwt'];

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(NoticeFactory::ERR_INVALID_ID_TOKEN);

        $this->noticeFactory->validate($message);
    }

    public function test_validate_state_returns_self()
    {
        $message = ['state' => 'test-state'];

        $result = $this->invokeMethod($this->noticeFactory, 'validateState', [$message]);

        $this->assertSame($this->noticeFactory, $result);
    }

    public function test_validate_nonce_throws_exception_for_missing_nonce()
    {
        $jwt = ['body' => []];
        $message = ['state' => 'test-state'];

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(NoticeFactory::ERR_MISSING_NONCE);

        $this->invokeMethod($this->noticeFactory, 'validateNonce', [$jwt, $message]);
    }

    public function test_validate_nonce_returns_self_with_valid_nonce()
    {
        $jwt = ['body' => ['nonce' => 'test-nonce']];
        $message = ['state' => 'test-state'];

        $result = $this->invokeMethod($this->noticeFactory, 'validateNonce', [$jwt, $message]);

        $this->assertSame($this->noticeFactory, $result);
    }

    public function test_create_message_returns_hello_world_notice()
    {
        $jwt = ['body' => [Claim::NOTICE => ['type' => LtiConstants::NOTICE_TYPE_HELLOWORLD]]];

        $message = $this->noticeFactory->createMessage($this->registrationMock, $jwt);

        $this->assertInstanceOf(HelloWorldNotice::class, $message);
    }

    public function test_create_message_returns_context_copy_notice()
    {
        $jwt = ['body' => [Claim::NOTICE => ['type' => LtiConstants::NOTICE_TYPE_CONTEXTCOPY]]];

        $message = $this->noticeFactory->createMessage($this->registrationMock, $jwt);

        $this->assertInstanceOf(\Packback\Lti1p3\Messages\ContextCopyNotice::class, $message);
    }

    public function test_create_message_returns_asset_processor_submission_notice()
    {
        $jwt = ['body' => [Claim::NOTICE => ['type' => LtiConstants::NOTICE_TYPE_ASSETPROCESSORSUBMISSION]]];

        $message = $this->noticeFactory->createMessage($this->registrationMock, $jwt);

        $this->assertInstanceOf(\Packback\Lti1p3\Messages\AssetProcessorSubmissionNotice::class, $message);
    }

    public function test_create_message_throws_exception_for_invalid_notice_type()
    {
        $jwt = ['body' => [Claim::NOTICE => ['type' => 'InvalidNoticeType']]];

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(NoticeFactory::ERR_INVALID_MESSAGE_TYPE);

        $this->noticeFactory->createMessage($this->registrationMock, $jwt);
    }

    public function test_validate_registration_throws_exception_for_missing_registration()
    {
        $jwtBody = [
            Version::claimKey() => LtiConstants::V1_3,
            'iss' => 'https://test.issuer.com',
            'aud' => 'test-client-id',
            'sub' => 'test-user',
            'nonce' => 'test-nonce',
            DeploymentId::claimKey() => 'test-deployment',
            Roles::claimKey() => ['Instructor'],
            Claim::NOTICE => ['type' => LtiConstants::NOTICE_TYPE_HELLOWORLD],
        ];

        $jwt = $this->createJwtToken($jwtBody);
        $message = ['jwt' => $jwt, 'state' => 'test-state'];

        $this->databaseMock->shouldReceive('findRegistrationByIssuer')
            ->with('https://test.issuer.com', 'test-client-id')
            ->andReturn(null);

        $this->expectException(LtiException::class);
        $this->expectExceptionMessageMatches('/LTI 1.3 Registration not found/');

        $this->noticeFactory->validate($message);
    }

    public function test_create_method_validates_and_creates_notice_message()
    {
        // Mock the validate method to return expected values
        $jwt = [
            'body' => [
                Version::claimKey() => LtiConstants::V1_3,
                'sub' => 'test-user',
                DeploymentId::claimKey() => 'test-deployment',
                Roles::claimKey() => ['Instructor'],
                Claim::NOTICE => ['type' => LtiConstants::NOTICE_TYPE_HELLOWORLD],
            ],
        ];
        $registration = $this->registrationMock;
        $deployment = null;

        // Create a partial mock to test the create method
        $factoryMock = Mockery::mock(NoticeFactory::class, [$this->databaseMock, $this->serviceConnectorMock])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $factoryMock->shouldReceive('validate')
            ->once()
            ->with(['test' => 'message'])
            ->andReturn([$jwt, $registration, $deployment]);

        $factoryMock->shouldReceive('validateClaims')
            ->once()
            ->with([], $jwt['body'])
            ->andReturn($factoryMock);

        $result = $factoryMock->create(['test' => 'message']);

        $this->assertInstanceOf(HelloWorldNotice::class, $result);
    }

    public function test_create_message_returns_notice_instance()
    {
        // Test the createMessage method functionality
        $jwtBody = [
            Version::claimKey() => LtiConstants::V1_3,
            'sub' => 'test-user',
            DeploymentId::claimKey() => 'test-deployment',
            Roles::claimKey() => ['Instructor'],
            Claim::NOTICE => ['type' => LtiConstants::NOTICE_TYPE_HELLOWORLD],
        ];

        $jwt = ['body' => $jwtBody];
        $registration = $this->registrationMock;

        $messageInstance = $this->noticeFactory->createMessage($registration, $jwt);

        $this->assertInstanceOf(HelloWorldNotice::class, $messageInstance);
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

    private function invokeMethod($object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
