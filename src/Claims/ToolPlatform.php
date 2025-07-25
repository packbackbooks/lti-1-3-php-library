<?php

namespace Packback\Lti1p3\Claims;

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
    public static function claimKey(): string
    {
        return Claim::TOOL_PLATFORM;
    }
}
