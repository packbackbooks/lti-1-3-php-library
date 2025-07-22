<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\LtiConstants;

class ContentItems extends Claim
{
    public static function key(): string
    {
        return LtiConstants::DL_CONTENT_ITEMS;
    }
}
