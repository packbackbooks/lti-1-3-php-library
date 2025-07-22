<?php

namespace Packback\Lti1p3\Claims;

class DeepLinkSettings extends Claim
{
    public static function key(): string
    {
        return Claim::DL_DEEP_LINK_SETTINGS;
    }
}
