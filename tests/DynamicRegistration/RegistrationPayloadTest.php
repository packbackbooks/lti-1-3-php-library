<?php

declare(strict_types=1);

namespace Tests\DynamicRegistration;

use Packback\Lti1p3\DynamicRegistration\RegistrationPayload;
use Tests\TestCase;

class RegistrationPayloadTest extends TestCase
{
    public function test_it_builds_basic_payload()
    {
        $result = $this->createPayload()->toArray();

        $this->assertEquals('web', $result['application_type']);
        $this->assertEquals(['id_token'], $result['response_types']);
        $this->assertEquals(['implicit', 'client_credentials'], $result['grant_types']);
        $this->assertEquals('https://tool.example.com/oidc', $result['initiate_login_uri']);
        $this->assertEquals(['https://tool.example.com/launch'], $result['redirect_uris']);
        $this->assertEquals('My Tool', $result['client_name']);
        $this->assertEquals('https://tool.example.com/jwks', $result['jwks_uri']);
        $this->assertEquals('private_key_jwt', $result['token_endpoint_auth_method']);
        $this->assertArrayNotHasKey('scope', $result);
    }

    public function test_it_filters_null_logo_uri()
    {
        $result = $this->createPayload()->toArray();

        $this->assertArrayNotHasKey('logo_uri', $result);
    }

    public function test_it_includes_logo_uri_when_provided()
    {
        $result = $this->createPayload(
            logoUrl: 'https://tool.example.com/logo.png',
        )->toArray();

        $this->assertEquals('https://tool.example.com/logo.png', $result['logo_uri']);
    }

    public function test_it_uses_custom_redirect_uris()
    {
        $redirectUris = [
            'https://tool.example.com/launch',
            'https://tool.example.com/callback',
        ];

        $result = $this->createPayload(
            redirectUris: $redirectUris,
        )->toArray();

        $this->assertEquals($redirectUris, $result['redirect_uris']);
    }

    public function test_it_joins_scopes()
    {
        $result = $this->createPayload(
            scopes: ['openid', 'profile', 'email'],
        )->toArray();

        $this->assertEquals('openid profile email', $result['scope']);
    }

    public function test_it_builds_lti_tool_configuration()
    {
        $result = $this->createPayload()->toArray();
        $ltiConfig = $result['https://purl.imsglobal.org/spec/lti-tool-configuration'];

        $this->assertEquals('tool.example.com', $ltiConfig['domain']);
        $this->assertEquals('A test tool', $ltiConfig['description']);
        $this->assertEquals('https://tool.example.com/launch', $ltiConfig['target_link_uri']);
        $this->assertEquals(
            ['iss', 'sub', 'name', 'email', 'given_name', 'family_name'],
            $ltiConfig['claims']
        );
    }

    public function test_it_builds_messages_array()
    {
        $result = $this->createPayload()->toArray();
        $messages = $result['https://purl.imsglobal.org/spec/lti-tool-configuration']['messages'];

        $this->assertCount(2, $messages);
        $this->assertEquals('LtiResourceLinkRequest', $messages[0]['type']);
        $this->assertEquals('https://tool.example.com/launch', $messages[0]['target_link_uri']);
        $this->assertEquals('LtiDeepLinkingRequest', $messages[1]['type']);
        $this->assertEquals('https://tool.example.com/launch', $messages[1]['target_link_uri']);
    }

    public function test_it_uses_empty_object_for_no_custom_parameters()
    {
        $result = $this->createPayload()->toArray();
        $ltiConfig = $result['https://purl.imsglobal.org/spec/lti-tool-configuration'];

        $json = json_encode($ltiConfig['custom_parameters']);
        $this->assertEquals('{}', $json);
    }

    public function test_it_uses_custom_parameters_when_provided()
    {
        $customParams = ['key1' => 'value1', 'key2' => 'value2'];

        $result = $this->createPayload(
            customParameters: $customParams,
        )->toArray();
        $ltiConfig = $result['https://purl.imsglobal.org/spec/lti-tool-configuration'];

        $this->assertEquals($customParams, $ltiConfig['custom_parameters']);
        $this->assertEquals($customParams, $ltiConfig['messages'][0]['custom_parameters']);
        $this->assertEquals($customParams, $ltiConfig['messages'][1]['custom_parameters']);
    }

    private function createPayload(
        ?string $logoUrl = null,
        array $scopes = [],
        array $redirectUris = [],
        array $customParameters = [],
    ): RegistrationPayload {
        return new RegistrationPayload(
            toolName: 'My Tool',
            toolDescription: 'A test tool',
            domain: 'tool.example.com',
            oidcInitiationUrl: 'https://tool.example.com/oidc',
            targetLinkUri: 'https://tool.example.com/launch',
            jwksUrl: 'https://tool.example.com/jwks',
            logoUrl: $logoUrl,
            scopes: $scopes,
            redirectUris: $redirectUris,
            customParameters: $customParameters,
        );
    }
}
