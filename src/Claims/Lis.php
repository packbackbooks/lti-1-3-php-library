<?php

namespace Packback\Lti1p3\Claims;

class Lis extends Claim
{
    public static function key(): string
    {
        return Claim::LIS;
    }
}
