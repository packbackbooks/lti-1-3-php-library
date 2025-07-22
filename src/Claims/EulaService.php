<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\LtiConstants;

class EulaService extends Claim
{
    public static function key(): string
    {
        return LtiConstants::EULA_CLAIM_SERVICE;
    }
}
