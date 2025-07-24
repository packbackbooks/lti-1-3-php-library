<?php

namespace Packback\Lti1p3\Claims;

/**
 * ToolPlatform Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti/claim/tool_platform
 *
 * No example found in test data.
 */
class ToolPlatform extends Claim
{
    public static function claimKey(): string
    {
        return Claim::TOOL_PLATFORM;
    }
}
