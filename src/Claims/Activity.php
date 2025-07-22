<?php

namespace Packback\Lti1p3\Claims;

class Activity extends Claim
{
    public static function key(): string
    {
        return Claim::ACTIVITY;
    }
}
