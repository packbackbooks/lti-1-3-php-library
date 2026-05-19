<?php

declare(strict_types=1);

namespace Packback\Lti1p3\DynamicRegistration;

use Packback\Lti1p3\LtiException;

class RegistrationResponse
{
    private const LTI_TOOL_CONFIG_KEY = 'https://purl.imsglobal.org/spec/lti-tool-configuration';

    public function __construct(
        public readonly string $clientId,
        public readonly ?string $deploymentId = null,
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
            throw new LtiException('Invalid JSON in registration response');
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
        if (empty($data['client_id'])) {
            throw new LtiException("Missing required field 'client_id' in registration response");
        }

        $deploymentId = $data[self::LTI_TOOL_CONFIG_KEY]['deployment_id'] ?? null;

        return new self(
            clientId: $data['client_id'],
            deploymentId: $deploymentId,
        );
    }
}
