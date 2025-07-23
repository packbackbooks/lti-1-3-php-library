<?php

namespace Packback\Lti1p3\Claims;

class NamesRoleProvisioningService extends Claim
{
    public static function claimKey(): string
    {
        return Claim::NRPS_NAMESROLESSERVICE;
    }
}
