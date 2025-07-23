<?php

namespace Packback\Lti1p3\Claims;

class Lis extends Claim
{
    public static function claimKey(): string
    {
        return Claim::LIS;
    }
}
