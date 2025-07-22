<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\LtiConstants;

class NamesRoleProvisioningService extends Claim
{
    public static function key(): string
    {
        return LtiConstants::NRPS_CLAIM_SERVICE;
    }
}
