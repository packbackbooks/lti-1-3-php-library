<?php

declare(strict_types=1);

namespace Packback\Lti1p3\DynamicRegistration;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Packback\Lti1p3\LtiException;

class DynamicRegistrationService
{
    private const TIMEOUT_SECONDS = 30;

    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Fetch the platform's OpenID Configuration.
     *
     * @throws LtiException
     */
    public function fetchOpenIdConfiguration(string $openidConfigurationUrl): OidcConfiguration
    {
        try {
            $response = $this->client->get($openidConfigurationUrl, [
                'timeout' => self::TIMEOUT_SECONDS,
            ]);
        } catch (GuzzleException $e) {
            throw new LtiException(
                "Failed to fetch OpenID Configuration from {$openidConfigurationUrl}: {$e->getMessage()}",
                0,
                $e,
            );
        }

        return OidcConfiguration::fromJson((string) $response->getBody());
    }

    /**
     * Register the tool with the platform.
     *
     * @throws LtiException
     */
    public function register(
        OidcConfiguration $config,
        RegistrationPayload $payload,
        ?string $registrationToken = null,
    ): RegistrationResponse {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        if ($registrationToken !== null) {
            $headers['Authorization'] = "Bearer {$registrationToken}";
        }

        try {
            $response = $this->client->post($config->registrationEndpoint, [
                'json' => $payload->toArray(),
                'headers' => $headers,
                'timeout' => self::TIMEOUT_SECONDS,
            ]);
        } catch (GuzzleException $e) {
            throw new LtiException(
                "Failed to register tool at {$config->registrationEndpoint}: {$e->getMessage()}",
                0,
                $e,
            );
        }

        return RegistrationResponse::fromJson((string) $response->getBody());
    }
}
