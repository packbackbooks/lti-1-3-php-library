<?php

namespace Packback\Lti1p3\Claims;

class NamesRoleProvisioningService extends Claim
{
    public static function key(): string
    {
        return Claim::NRPS_NAMESROLESSERVICE;
    }
}
