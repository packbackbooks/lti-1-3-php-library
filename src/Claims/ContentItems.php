<?php

namespace Packback\Lti1p3\Claims;

class ContentItems extends Claim
{
    public static function key(): string
    {
        return Claim::DL_CONTENT_ITEMS;
    }
}
