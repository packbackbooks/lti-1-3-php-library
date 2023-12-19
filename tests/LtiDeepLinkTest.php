<?php

namespace Tests;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Mockery;
use Packback\Lti1p3\DeepLinkResources\Resource;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\LtiDeepLink;

class LtiDeepLinkTest extends TestCase
{
    public const CLIENT_ID = 'client-id';
    public const ISSUER = 'issuer';
    public const DEPLOYMENT_ID = 'deployment-id';
    public const LTI_RESOURCE_ARRAY = ['resource'];
    private $registrationMock;
    private $resourceMock;

    protected function setUp(): void
    {
        $this->registrationMock = Mockery::mock(ILtiRegistration::class);
        $this->resourceMock = Mockery::mock(Resource::class);
    }

    public function testItInstantiates()
    {
        $registration = Mockery::mock(ILtiRegistration::class);

        $deepLink = new LtiDeepLink($registration, 'test', []);

        $this->assertInstanceOf(LtiDeepLink::class, $deepLink);
    }

    public function testItGetsJwtResponse()
    {
        $this->setupMocksExpectations();

        $deepLink = new LtiDeepLink($this->registrationMock, self::DEPLOYMENT_ID, []);

        $result = $deepLink->getResponseJwt([$this->resourceMock]);

        $publicKey = new Key(file_get_contents(__DIR__.'/data/public.key'), 'RS256');
        $resultPayload = JWT::decode($result, $publicKey);

        $this->assertEquals(self::CLIENT_ID, $resultPayload->iss);
        $this->assertEquals([self::ISSUER], $resultPayload->aud);
        $this->assertEquals($resultPayload->exp, $resultPayload->iat + 600);
        $this->assertStringStartsWith('nonce-', $resultPayload->nonce);
        $this->assertEquals(self::DEPLOYMENT_ID, $resultPayload->{LtiConstants::DEPLOYMENT_ID});
        $this->assertEquals(LtiConstants::MESSAGE_TYPE_DEEPLINK_RESPONSE, $resultPayload->{LtiConstants::MESSAGE_TYPE});
        $this->assertEquals(LtiConstants::V1_3, $resultPayload->{LtiConstants::VERSION});
        $this->assertEquals([self::LTI_RESOURCE_ARRAY], $resultPayload->{LtiConstants::DL_CONTENT_ITEMS});
    }

    public function testJwtResponseDoesNotContainDataPropertyWhenNotSet()
    {
        $this->setupMocksExpectations();

        $deepLink = new LtiDeepLink($this->registrationMock, self::DEPLOYMENT_ID, []);

        $result = $deepLink->getResponseJwt([$this->resourceMock]);

        $publicKey = new Key(file_get_contents(__DIR__.'/data/public.key'), 'RS256');
        $resultPayload = JWT::decode($result, $publicKey);

        $this->assertArrayNotHasKey(LtiConstants::DL_DATA, get_object_vars($resultPayload));
    }

    public function testJwtResponseContainsDataPropertyWhenSet()
    {
        $this->setupMocksExpectations();

        $dataValue = 'value';

        $deepLink = new LtiDeepLink($this->registrationMock, self::DEPLOYMENT_ID, [
            'data' => $dataValue,
        ]);

        $result = $deepLink->getResponseJwt([$this->resourceMock]);

        $publicKey = new Key(file_get_contents(__DIR__.'/data/public.key'), 'RS256');
        $resultPayload = JWT::decode($result, $publicKey);

        $this->assertEquals($dataValue, $resultPayload->{LtiConstants::DL_DATA});
    }

    private function setupMocksExpectations(): void
    {
        $this->registrationMock
            ->shouldReceive('getClientId')
            ->once()
            ->andReturn(self::CLIENT_ID);
        $this->registrationMock
            ->shouldReceive('getIssuer')
            ->once()
            ->andReturn(self::ISSUER);
        $this->registrationMock
            ->shouldReceive('getToolPrivateKey')
            ->once()
            ->andReturn(file_get_contents(__DIR__.'/data/private.key'));
        $this->registrationMock
            ->shouldReceive('getKid')
            ->once()
            ->andReturn('kid');

        $this->resourceMock
            ->shouldReceive('toArray')
            ->once()
            ->andReturn(self::LTI_RESOURCE_ARRAY);
    }
}
