<?php

namespace Packback\Lti1p3\Claims;

class Context extends Claim
{
    public static function key(): string
    {
        return Claim::CONTEXT;
    }
}
