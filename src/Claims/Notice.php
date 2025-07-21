<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\LtiConstants;

class Notice extends Claim
{
    public static function key(): string
    {
        return LtiConstants::PNS_CLAIM_NOTICE;
    }
}
