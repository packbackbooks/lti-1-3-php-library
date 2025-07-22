<?php

namespace Packback\Lti1p3\Claims;

class RoleScopeMentor extends Claim
{
    public static function key(): string
    {
        return Claim::ROLE_SCOPE_MENTOR;
    }
}
