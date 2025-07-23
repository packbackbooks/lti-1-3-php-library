<?php

namespace Packback\Lti1p3\Claims;

class DeepLinkSettings extends Claim
{
    public static function claimKey(): string
    {
        return Claim::DL_DEEP_LINK_SETTINGS;
    }
}
