<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\LtiConstants;

class Lti1p1 extends Claim
{
    public static function key(): string
    {
        return LtiConstants::LTI1P1;
    }
}
