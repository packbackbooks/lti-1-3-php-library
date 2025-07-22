<?php

namespace Packback\Lti1p3\Claims;

class Notice extends Claim
{
    public static function key(): string
    {
        return Claim::NOTICE;
    }
}
