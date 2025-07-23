<?php

namespace Packback\Lti1p3\Claims;

class Roles extends Claim
{
    public static function claimKey(): string
    {
        return Claim::ROLES;
    }
}
