<?php

declare(strict_types=1);

namespace Tests\DynamicRegistration;

use Packback\Lti1p3\DynamicRegistration\OidcConfiguration;
use Packback\Lti1p3\LtiException;
use Tests\TestCase;

class OidcConfigurationTest extends TestCase
{
    private array $validData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validData = [
            'issuer' => 'https://platform.example.com',
            'authorization_endpoint' => 'https://platform.example.com/auth',
            'token_endpoint' => 'https://platform.example.com/token',
            'jwks_uri' => 'https://platform.example.com/jwks',
            'registration_endpoint' => 'https://platform.example.com/register',
            'scopes_supported' => ['openid', 'profile'],
            'claims_supported' => ['sub', 'name'],
        ];
    }

    public static function missingFieldProvider(): array
    {
        return [
            'issuer' => ['issuer'],
            'authorization_endpoint' => ['authorization_endpoint'],
            'token_endpoint' => ['token_endpoint'],
            'jwks_uri' => ['jwks_uri'],
            'registration_endpoint' => ['registration_endpoint'],
        ];
    }

    public function test_it_creates_from_array()
    {
        $config = OidcConfiguration::fromArray($this->validData);

        $this->assertInstanceOf(OidcConfiguration::class, $config);
        $this->assertEquals('https://platform.example.com', $config->issuer);
        $this->assertEquals('https://platform.example.com/auth', $config->authorizationEndpoint);
        $this->assertEquals('https://platform.example.com/token', $config->tokenEndpoint);
        $this->assertEquals('https://platform.example.com/jwks', $config->jwksUri);
        $this->assertEquals('https://platform.example.com/register', $config->registrationEndpoint);
        $this->assertEquals(['openid', 'profile'], $config->scopesSupported);
        $this->assertEquals(['sub', 'name'], $config->claimsSupported);
    }

    public function test_it_creates_from_array_with_defaults()
    {
        $data = $this->validData;
        unset($data['scopes_supported'], $data['claims_supported']);

        $config = OidcConfiguration::fromArray($data);

        $this->assertEquals([], $config->scopesSupported);
        $this->assertEquals([], $config->claimsSupported);
    }

    public function test_it_creates_from_json()
    {
        $json = json_encode($this->validData);

        $config = OidcConfiguration::fromJson($json);

        $this->assertInstanceOf(OidcConfiguration::class, $config);
        $this->assertEquals('https://platform.example.com', $config->issuer);
    }

    public function test_it_throws_on_invalid_json()
    {
        $this->expectException(LtiException::class);
        $this->expectExceptionMessage('Invalid JSON in OpenID Configuration');

        OidcConfiguration::fromJson('not valid json');
    }

    /**
     * @dataProvider missingFieldProvider
     */
    public function test_it_throws_on_missing_required_field(string $field)
    {
        $data = $this->validData;
        unset($data[$field]);

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage("Missing required field '{$field}' in OpenID Configuration");

        OidcConfiguration::fromArray($data);
    }

    /**
     * @dataProvider missingFieldProvider
     */
    public function test_it_throws_on_empty_required_field(string $field)
    {
        $data = $this->validData;
        $data[$field] = '';

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage("Missing required field '{$field}' in OpenID Configuration");

        OidcConfiguration::fromArray($data);
    }
}
