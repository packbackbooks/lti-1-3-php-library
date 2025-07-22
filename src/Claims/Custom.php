<?php

namespace Packback\Lti1p3\Claims;

class Custom extends Claim
{
    public static function key(): string
    {
        return Claim::CUSTOM;
    }
}
