<?php

namespace Packback\Lti1p3\Claims;

class DeploymentId extends Claim
{
    public static function claimKey(): string
    {
        return Claim::DEPLOYMENT_ID;
    }
}
