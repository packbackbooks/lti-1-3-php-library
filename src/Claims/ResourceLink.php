<?php

namespace Packback\Lti1p3\Claims;

class ResourceLink extends Claim
{
    public static function claimKey(): string
    {
        return Claim::RESOURCE_LINK;
    }
}
