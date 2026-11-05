<?php

declare(strict_types=1);

namespace Packback\Lti1p3\DynamicRegistration;

use Packback\Lti1p3\LtiException;

class OidcConfiguration
{
    private const REQUIRED_FIELDS = [
        'issuer',
        'authorization_endpoint',
        'token_endpoint',
        'jwks_uri',
        'registration_endpoint',
    ];

    public function __construct(
        public readonly string $issuer,
        public readonly string $authorizationEndpoint,
        public readonly string $tokenEndpoint,
        public readonly string $jwksUri,
        public readonly string $registrationEndpoint,
        public readonly array $scopesSupported = [],
        public readonly array $claimsSupported = [],
    ) {}

    /**
     * Create from a JSON string.
     *
     * @throws LtiException
     */
    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true);

        if (!is_array($data)) {
            throw new LtiException('Invalid JSON in OpenID Configuration');
        }

        return self::fromArray($data);
    }

    /**
     * Create from an associative array.
     *
     * @throws LtiException
     */
    public static function fromArray(array $data): self
    {
        foreach (self::REQUIRED_FIELDS as $field) {
            if (empty($data[$field])) {
                throw new LtiException("Missing required field '{$field}' in OpenID Configuration");
            }
        }

        return new self(
            issuer: $data['issuer'],
            authorizationEndpoint: $data['authorization_endpoint'],
            tokenEndpoint: $data['token_endpoint'],
            jwksUri: $data['jwks_uri'],
            registrationEndpoint: $data['registration_endpoint'],
            scopesSupported: $data['scopes_supported'] ?? [],
            claimsSupported: $data['claims_supported'] ?? [],
        );
    }
}
