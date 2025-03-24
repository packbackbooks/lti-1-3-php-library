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
    private $settings = [
        'accept_types' => ['link', 'file', 'html', 'ltiResourceLink', 'image'],
        'accept_media_types' => 'image/:::asterisk:::,text/html',
        'accept_presentation_document_targets' => ['iframe', 'window', 'embed'],
        'accept_multiple' => true,
        'accept_lineitem' => true,
        'auto_create' => true,
        'title' => 'This is the default title',
        'text' => 'This is the default text',
        'data' => 'Some random opaque data that MUST be sent back',
        'deep_link_return_url' => 'https://platform.example/deep_links',
    ];

    protected function setUp(): void
    {
        $this->registrationMock = Mockery::mock(ILtiRegistration::class);
        $this->resourceMock = Mockery::mock(Resource::class);
    }

    public function test_it_instantiates()
    {
        $registration = Mockery::mock(ILtiRegistration::class);

        $deepLink = new LtiDeepLink($registration, 'deployment_id', []);

        $this->assertInstanceOf(LtiDeepLink::class, $deepLink);
    }

    public function test_it_gets_jwt_response()
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

    public function test_jwt_response_does_not_contain_data_property_when_not_set()
    {
        $this->setupMocksExpectations();

        $deepLink = new LtiDeepLink($this->registrationMock, self::DEPLOYMENT_ID, []);

        $result = $deepLink->getResponseJwt([$this->resourceMock]);

        $publicKey = new Key(file_get_contents(__DIR__.'/data/public.key'), 'RS256');
        $resultPayload = JWT::decode($result, $publicKey);

        $this->assertArrayNotHasKey(LtiConstants::DL_DATA, get_object_vars($resultPayload));
    }

    public function test_jwt_response_contains_data_property_when_set()
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

    public function test_settings()
    {
        $registration = Mockery::mock(ILtiRegistration::class);
        $deepLink = new LtiDeepLink($registration, 'deployment_id', $this->settings);

        $this->assertEquals($this->settings, $deepLink->settings());
    }

    public function test_return_url()
    {
        $registration = Mockery::mock(ILtiRegistration::class);
        $returnUrl = 'https://google.com';
        $deepLink = new LtiDeepLink($registration, 'deployment_id', $this->settings);

        $this->assertEquals($this->settings['deep_link_return_url'], $deepLink->returnUrl());
    }

    public function test_accept_types()
    {
        $registration = Mockery::mock(ILtiRegistration::class);
        $deepLink = new LtiDeepLink($registration, 'deployment_id', $this->settings);

        $this->assertEquals($this->settings['accept_types'], $deepLink->acceptTypes());
    }

    public function test_can_accept_type()
    {
        $registration = Mockery::mock(ILtiRegistration::class);
        $deepLink = new LtiDeepLink($registration, 'deployment_id', $this->settings);

        $this->assertFalse($deepLink->canAcceptType('foo'));
        foreach ($this->settings['accept_types'] as $type) {
            $this->assertTrue($deepLink->canAcceptType($type));
        }
    }

    public function test_accept_presentation_document_targets()
    {
        $registration = Mockery::mock(ILtiRegistration::class);
        $deepLink = new LtiDeepLink($registration, 'deployment_id', $this->settings);

        $this->assertEquals($this->settings['accept_presentation_document_targets'], $deepLink->acceptPresentationDocumentTargets());
    }

    public function test_can_accept_presentation_document_target()
    {
        $registration = Mockery::mock(ILtiRegistration::class);
        $deepLink = new LtiDeepLink($registration, 'deployment_id', $this->settings);

        $this->assertFalse($deepLink->canAcceptPresentationDocumentTarget('foo'));
        foreach ($this->settings['accept_presentation_document_targets'] as $type) {
            $this->assertTrue($deepLink->canAcceptPresentationDocumentTarget($type));
        }
    }

    public function test_accept_media_types()
    {
        $registration = Mockery::mock(ILtiRegistration::class);
        $deepLink = new LtiDeepLink($registration, 'deployment_id', $this->settings);

        $this->assertEquals($this->settings['accept_media_types'], $deepLink->acceptMediaTypes());
    }

    public function test_can_accept_multiple()
    {
        $registration = Mockery::mock(ILtiRegistration::class);
        $deepLink = new LtiDeepLink($registration, 'deployment_id', $this->settings);
        $this->assertTrue($deepLink->canAcceptMultiple());

        $deepLink = new LtiDeepLink($registration, 'deployment_id', []);
        $this->assertFalse($deepLink->canAcceptMultiple());
    }

    public function test_can_accept_lineitem()
    {
        $registration = Mockery::mock(ILtiRegistration::class);

        $deepLink = new LtiDeepLink($registration, 'deployment_id', $this->settings);
        $this->assertTrue($deepLink->canAcceptLineitem());

        $deepLink = new LtiDeepLink($registration, 'deployment_id', []);
        $this->assertFalse($deepLink->canAcceptLineitem());
    }

    public function test_can_auto_create()
    {
        $registration = Mockery::mock(ILtiRegistration::class);

        $deepLink = new LtiDeepLink($registration, 'deployment_id', $this->settings);
        $this->assertTrue($deepLink->canAutoCreate());

        $deepLink = new LtiDeepLink($registration, 'deployment_id', []);
        $this->assertFalse($deepLink->canAutoCreate());
    }

    public function test_title()
    {
        $registration = Mockery::mock(ILtiRegistration::class);

        $deepLink = new LtiDeepLink($registration, 'deployment_id', $this->settings);
        $this->assertEquals($this->settings['title'], $deepLink->title());

        $deepLink = new LtiDeepLink($registration, 'deployment_id', []);
        $this->assertNull($deepLink->title());
    }

    public function test_text()
    {
        $registration = Mockery::mock(ILtiRegistration::class);

        $deepLink = new LtiDeepLink($registration, 'deployment_id', $this->settings);
        $this->assertEquals($this->settings['text'], $deepLink->text());

        $deepLink = new LtiDeepLink($registration, 'deployment_id', []);
        $this->assertNull($deepLink->text());
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
