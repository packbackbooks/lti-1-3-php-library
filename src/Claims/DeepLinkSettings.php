<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\LtiConstants;

class DeepLinkSettings extends Claim
{
    public static function key(): string
    {
        return LtiConstants::DL_DEEP_LINK_SETTINGS;
    }
}
