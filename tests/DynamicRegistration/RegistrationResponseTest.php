<?php

declare(strict_types=1);

namespace Tests\DynamicRegistration;

use Packback\Lti1p3\DynamicRegistration\RegistrationResponse;
use Packback\Lti1p3\LtiException;
use Tests\TestCase;

class RegistrationResponseTest extends TestCase
{
    public function test_it_creates_from_array_with_deployment_id()
    {
        $data = [
            'client_id' => 'test-client-id',
            'https://purl.imsglobal.org/spec/lti-tool-configuration' => [
                'deployment_id' => 'test-deployment-id',
            ],
        ];

        $response = RegistrationResponse::fromArray($data);

        $this->assertInstanceOf(RegistrationResponse::class, $response);
        $this->assertEquals('test-client-id', $response->clientId);
        $this->assertEquals('test-deployment-id', $response->deploymentId);
    }

    public function test_it_creates_from_array_without_deployment_id()
    {
        $data = [
            'client_id' => 'test-client-id',
        ];

        $response = RegistrationResponse::fromArray($data);

        $this->assertEquals('test-client-id', $response->clientId);
        $this->assertNull($response->deploymentId);
    }

    public function test_it_creates_from_array_with_empty_lti_config()
    {
        $data = [
            'client_id' => 'test-client-id',
            'https://purl.imsglobal.org/spec/lti-tool-configuration' => [],
        ];

        $response = RegistrationResponse::fromArray($data);

        $this->assertEquals('test-client-id', $response->clientId);
        $this->assertNull($response->deploymentId);
    }

    public function test_it_creates_from_json()
    {
        $json = json_encode([
            'client_id' => 'test-client-id',
            'https://purl.imsglobal.org/spec/lti-tool-configuration' => [
                'deployment_id' => 'test-deployment-id',
            ],
        ]);

        $response = RegistrationResponse::fromJson($json);

        $this->assertInstanceOf(RegistrationResponse::class, $response);
        $this->assertEquals('test-client-id', $response->clientId);
        $this->assertEquals('test-deployment-id', $response->deploymentId);
    }

    public function test_it_throws_on_invalid_json()
    {
        $this->expectException(LtiException::class);
        $this->expectExceptionMessage('Invalid JSON in registration response');

        RegistrationResponse::fromJson('not valid json');
    }

    public function test_it_throws_on_missing_client_id()
    {
        $this->expectException(LtiException::class);
        $this->expectExceptionMessage("Missing required field 'client_id' in registration response");

        RegistrationResponse::fromArray([]);
    }

    public function test_it_throws_on_empty_client_id()
    {
        $this->expectException(LtiException::class);
        $this->expectExceptionMessage("Missing required field 'client_id' in registration response");

        RegistrationResponse::fromArray(['client_id' => '']);
    }
}
