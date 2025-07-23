<?php

namespace Packback\Lti1p3\Claims;

class ToolPlatform extends Claim
{
    public static function claimKey(): string
    {
        return Claim::TOOL_PLATFORM;
    }
}
