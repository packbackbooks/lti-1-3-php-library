<?php

namespace Packback\Lti1p3\Claims;

class Custom extends Claim
{
    public static function claimKey(): string
    {
        return Claim::CUSTOM;
    }
}
