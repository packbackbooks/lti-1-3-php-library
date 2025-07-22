<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\LtiConstants;

class GroupService extends Claim
{
    public static function key(): string
    {
        return LtiConstants::GS_CLAIM_SERVICE;
    }
}
