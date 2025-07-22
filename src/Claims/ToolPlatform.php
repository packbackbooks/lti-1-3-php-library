<?php

namespace Packback\Lti1p3\Claims;

class ToolPlatform extends Claim
{
    public static function key(): string
    {
        return Claim::TOOL_PLATFORM;
    }
}
