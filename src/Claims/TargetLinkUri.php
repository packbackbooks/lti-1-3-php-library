<?php

namespace Packback\Lti1p3\Claims;

class TargetLinkUri extends Claim
{
    public static function key(): string
    {
        return Claim::TARGET_LINK_URI;
    }
}
