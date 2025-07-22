<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\LtiConstants;

class RoleScopeMentor extends Claim
{
    public static function key(): string
    {
        return LtiConstants::ROLE_SCOPE_MENTOR;
    }
}
