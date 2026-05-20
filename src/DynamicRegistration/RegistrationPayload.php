<?php

declare(strict_types=1);

namespace Packback\Lti1p3\DynamicRegistration;

use Packback\Lti1p3\Concerns\Arrayable;
use Packback\Lti1p3\LtiConstants;

class RegistrationPayload
{
    use Arrayable;

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

    public function getArray(): array
    {
        $customParams = !empty($this->customParameters)
            ? $this->customParameters
            : (object) [];

        return [
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
            'scope' => !empty($this->scopes)
                ? implode(' ', $this->scopes)
                : null,
            'logo_uri' => $this->logoUrl,
            LtiConstants::LTI_TOOL_CONFIGURATION => [
                'domain' => $this->domain,
                'description' => $this->toolDescription,
                'target_link_uri' => $this->targetLinkUri,
                'custom_parameters' => $customParams,
                'claims' => ['iss', 'sub', 'name', 'email', 'given_name', 'family_name'],
                'messages' => [
                    [
                        'type' => LtiConstants::MESSAGE_TYPE_RESOURCE,
                        'target_link_uri' => $this->targetLinkUri,
                        'custom_parameters' => $customParams,
                    ],
                    [
                        'type' => LtiConstants::MESSAGE_TYPE_DEEPLINK,
                        'target_link_uri' => $this->targetLinkUri,
                        'custom_parameters' => $customParams,
                    ],
                ],
            ],
        ];
    }
}
