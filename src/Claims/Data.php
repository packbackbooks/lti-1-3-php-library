<?php

namespace Packback\Lti1p3\Claims;

class Data extends Claim
{
    public static function key(): string
    {
        return Claim::DL_DATA;
    }
}
