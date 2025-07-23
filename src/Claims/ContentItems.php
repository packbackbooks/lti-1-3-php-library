<?php

namespace Packback\Lti1p3\Claims;

class ContentItems extends Claim
{
    public static function claimKey(): string
    {
        return Claim::DL_CONTENT_ITEMS;
    }
}
