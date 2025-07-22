<?php

namespace Packback\Lti1p3\Claims;

class Roles extends Claim
{
    public static function key(): string
    {
        return Claim::ROLES;
    }
}
