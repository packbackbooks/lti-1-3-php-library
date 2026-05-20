<?php

declare(strict_types=1);

namespace Tests\DynamicRegistration;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mockery;
use Packback\Lti1p3\DynamicRegistration\DynamicRegistrationService;
use Packback\Lti1p3\DynamicRegistration\OidcConfiguration;
use Packback\Lti1p3\DynamicRegistration\RegistrationPayload;
use Packback\Lti1p3\LtiException;
use Tests\TestCase;

class DynamicRegistrationServiceTest extends TestCase
{
    private $client;
    private DynamicRegistrationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Mockery::mock(Client::class);
        $this->service = new DynamicRegistrationService($this->client);
    }

    public function test_it_instantiates()
    {
        $this->assertInstanceOf(DynamicRegistrationService::class, $this->service);
    }

    public function test_it_fetches_openid_configuration()
    {
        $url = 'https://platform.example.com/.well-known/openid-configuration';
        $responseBody = json_encode([
            'issuer' => 'https://platform.example.com',
            'authorization_endpoint' => 'https://platform.example.com/auth',
            'token_endpoint' => 'https://platform.example.com/token',
            'jwks_uri' => 'https://platform.example.com/jwks',
            'registration_endpoint' => 'https://platform.example.com/register',
        ]);

        $this->client
            ->shouldReceive('get')
            ->once()
            ->with($url, ['timeout' => 30])
            ->andReturn(new Response(200, [], $responseBody));

        $config = $this->service->fetchOpenIdConfiguration($url);

        $this->assertInstanceOf(OidcConfiguration::class, $config);
        $this->assertEquals('https://platform.example.com', $config->issuer);
        $this->assertEquals('https://platform.example.com/register', $config->registrationEndpoint);
    }

    public function test_it_throws_on_fetch_http_failure()
    {
        $url = 'https://platform.example.com/.well-known/openid-configuration';

        $this->client
            ->shouldReceive('get')
            ->once()
            ->andThrow(new RequestException(
                'Connection timed out',
                new Request('GET', $url),
            ));

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage("Failed to fetch OpenID Configuration from {$url}: Connection timed out");

        $this->service->fetchOpenIdConfiguration($url);
    }

    public function test_it_registers_tool()
    {
        $config = $this->createTestConfig();
        $payload = $this->createTestPayload();

        $responseBody = json_encode([
            'client_id' => 'new-client-id',
            'https://purl.imsglobal.org/spec/lti-tool-configuration' => [
                'deployment_id' => 'new-deployment-id',
            ],
        ]);

        $this->client
            ->shouldReceive('post')
            ->once()
            ->with('https://platform.example.com/register', Mockery::on(function ($options) {
                return $options['headers']['Content-Type'] === 'application/json'
                    && $options['headers']['Accept'] === 'application/json'
                    && !isset($options['headers']['Authorization'])
                    && $options['timeout'] === 30
                    && is_array($options['json']);
            }))
            ->andReturn(new Response(200, [], $responseBody));

        $response = $this->service->register($config, $payload);

        $this->assertEquals('new-client-id', $response->clientId);
        $this->assertEquals('new-deployment-id', $response->deploymentId);
    }

    public function test_it_registers_tool_with_bearer_token()
    {
        $config = $this->createTestConfig();
        $payload = $this->createTestPayload();

        $responseBody = json_encode(['client_id' => 'new-client-id']);

        $this->client
            ->shouldReceive('post')
            ->once()
            ->with('https://platform.example.com/register', Mockery::on(function ($options) {
                return $options['headers']['Authorization'] === 'Bearer my-secret-token';
            }))
            ->andReturn(new Response(200, [], $responseBody));

        $response = $this->service->register($config, $payload, 'my-secret-token');

        $this->assertEquals('new-client-id', $response->clientId);
    }

    public function test_it_throws_on_register_http_failure()
    {
        $config = $this->createTestConfig();
        $payload = $this->createTestPayload();

        $this->client
            ->shouldReceive('post')
            ->once()
            ->andThrow(new RequestException(
                'Server error',
                new Request('POST', 'https://platform.example.com/register'),
            ));

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage('Failed to register tool at https://platform.example.com/register: Server error');

        $this->service->register($config, $payload);
    }

    private function createTestConfig(): OidcConfiguration
    {
        return new OidcConfiguration(
            issuer: 'https://platform.example.com',
            authorizationEndpoint: 'https://platform.example.com/auth',
            tokenEndpoint: 'https://platform.example.com/token',
            jwksUri: 'https://platform.example.com/jwks',
            registrationEndpoint: 'https://platform.example.com/register',
        );
    }

    private function createTestPayload(): RegistrationPayload
    {
        return new RegistrationPayload(
            toolName: 'My Tool',
            toolDescription: 'A test tool',
            domain: 'tool.example.com',
            oidcInitiationUrl: 'https://tool.example.com/oidc',
            targetLinkUri: 'https://tool.example.com/launch',
            jwksUrl: 'https://tool.example.com/jwks',
        );
    }
}
