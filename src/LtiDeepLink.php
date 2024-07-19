<?php

namespace Packback\Lti1p3;

use Firebase\JWT\JWT;
use Packback\Lti1p3\Interfaces\ILtiRegistration;

class LtiDeepLink
{
    public function __construct(
        private ILtiRegistration $registration,
        private string $deployment_id,
        private array $deep_link_settings
    ) {}

    public function getResponseJwt(array $resources): string
    {
        $message_jwt = [
            'iss' => $this->registration->getClientId(),
            'aud' => [$this->registration->getIssuer()],
            'exp' => time() + 600,
            'iat' => time(),
            'nonce' => LtiOidcLogin::secureRandomString('nonce-'),
            LtiConstants::DEPLOYMENT_ID => $this->deployment_id,
            LtiConstants::MESSAGE_TYPE => LtiConstants::MESSAGE_TYPE_DEEPLINK_RESPONSE,
            LtiConstants::VERSION => LtiConstants::V1_3,
            LtiConstants::DL_CONTENT_ITEMS => array_map(function ($resource) {
                return $resource->toArray();
            }, $resources),
        ];

        // https://www.imsglobal.org/spec/lti-dl/v2p0/#deep-linking-request-message
        // 'data' is an optional property which, if it exists, must be returned by the tool
        if (isset($this->settings()['data'])) {
            $message_jwt[LtiConstants::DL_DATA] = $this->settings()['data'];
        }

        return JWT::encode($message_jwt, $this->registration->getToolPrivateKey(), 'RS256', $this->registration->getKid());
    }

    public function settings(): array
    {
        return $this->deep_link_settings;
    }

    public function returnUrl(): string
    {
        return $this->settings()['deep_link_return_url'];
    }

    public function accepts(): array
    {
        return $this->settings()['accept_types'];
    }

    public function canAccept(string $acceptType): bool
    {
        return in_array($acceptType, $this->accepts());
    }
}
