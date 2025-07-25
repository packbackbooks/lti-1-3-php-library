<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\Claims\Concerns\HasErrors;

/**
 * ToolPlatform Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti/claim/tool_platform
 *
 * Example payload:
 * {
 *     "https://purl.imsglobal.org/spec/lti/claim/tool_platform": {
 *         "guid": "KnQbfmlzZWjswfYmnKN7QKTohFOeRn8Jtm6R5GGw:canvas-lms",
 *         "name": "Packback Engineering",
 *         "version": "cloud",
 *         "product_family_code": "canvas",
 *         "validation_context": null,
 *         "errors": {
 *             "errors": {}
 *         }
 *     }
 * }
 */
class ToolPlatform extends Claim
{
    use HasErrors;

    public static function claimKey(): string
    {
        return Claim::TOOL_PLATFORM;
    }

    public function guid(): ?string
    {
        return $this->getBody()['guid'] ?? null;
    }

    public function name(): ?string
    {
        return $this->getBody()['name'] ?? null;
    }

    public function version(): ?string
    {
        return $this->getBody()['version'] ?? null;
    }

    public function productFamilyCode(): ?string
    {
        return $this->getBody()['product_family_code'] ?? null;
    }

    public function validationContext(): ?array
    {
        return $this->getBody()['validation_context'] ?? null;
    }
}
