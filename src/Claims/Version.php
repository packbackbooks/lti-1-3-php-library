<?php

namespace Packback\Lti1p3\Claims;

class Version extends Claim
{
    public static function key(): string
    {
        return Claim::VERSION;
    }
}
