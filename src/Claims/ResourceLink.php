<?php

namespace Packback\Lti1p3\Claims;

class ResourceLink extends Claim
{
    public static function key(): string
    {
        return Claim::RESOURCE_LINK;
    }
}
