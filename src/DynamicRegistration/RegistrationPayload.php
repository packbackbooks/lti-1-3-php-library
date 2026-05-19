<?php

declare(strict_types=1);

namespace Packback\Lti1p3\DynamicRegistration;

class RegistrationPayload
{
    private const LTI_TOOL_CONFIG_KEY = 'https://purl.imsglobal.org/spec/lti-tool-configuration';
    private const MESSAGE_TYPE_RESOURCE_LINK = 'LtiResourceLinkRequest';
    private const MESSAGE_TYPE_DEEP_LINKING = 'LtiDeepLinkingRequest';

    public function __construct(
        private readonly string $toolName,
        private readonly string $toolDescription,
        private readonly string $domain,
        private readonly string $oidcInitiationUrl,
        private readonly string $targetLinkUri,
        private readonly string $jwksUrl,
        private readonly ?string $logoUrl = null,
        private readonly array $scopes = [],
        private readonly array $redirectUris = [],
        private readonly array $customParameters = [],
    ) {}

    /**
     * Build the registration POST body per the IMS spec.
     */
    public function toArray(): array
    {
        $customParams = !empty($this->customParameters)
            ? $this->customParameters
            : (object) [];

        $payload = [
            'application_type' => 'web',
            'response_types' => ['id_token'],
            'grant_types' => ['implicit', 'client_credentials'],
            'initiate_login_uri' => $this->oidcInitiationUrl,
            'redirect_uris' => !empty($this->redirectUris)
                ? $this->redirectUris
                : [$this->targetLinkUri],
            'client_name' => $this->toolName,
            'jwks_uri' => $this->jwksUrl,
            'token_endpoint_auth_method' => 'private_key_jwt',
            self::LTI_TOOL_CONFIG_KEY => [
                'domain' => $this->domain,
                'description' => $this->toolDescription,
                'target_link_uri' => $this->targetLinkUri,
                'custom_parameters' => $customParams,
                'claims' => ['iss', 'sub', 'name', 'email', 'given_name', 'family_name'],
                'messages' => [
                    [
                        'type' => self::MESSAGE_TYPE_RESOURCE_LINK,
                        'target_link_uri' => $this->targetLinkUri,
                        'custom_parameters' => $customParams,
                    ],
                    [
                        'type' => self::MESSAGE_TYPE_DEEP_LINKING,
                        'target_link_uri' => $this->targetLinkUri,
                        'custom_parameters' => $customParams,
                    ],
                ],
            ],
        ];

        if (!empty($this->scopes)) {
            $payload['scope'] = implode(' ', $this->scopes);
        }

        if ($this->logoUrl !== null) {
            $payload['logo_uri'] = $this->logoUrl;
        }

        return $payload;
    }
}
