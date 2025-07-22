<?php

namespace Packback\Lti1p3\Claims;

class Lti1p1 extends Claim
{
    public static function key(): string
    {
        return Claim::LTI1P1;
    }
}
