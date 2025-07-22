<?php

namespace Packback\Lti1p3\Claims;

class DeploymentId extends Claim
{
    public static function key(): string
    {
        return Claim::DEPLOYMENT_ID;
    }
}
