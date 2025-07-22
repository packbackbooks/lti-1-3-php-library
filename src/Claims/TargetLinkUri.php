<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\LtiConstants;

class TargetLinkUri extends Claim
{
    public static function key(): string
    {
        return LtiConstants::TARGET_LINK_URI;
    }
}
