<?php

namespace Packback\Lti1p3\Claims;

class Asset extends Claim
{
    public static function key(): string
    {
        return Claim::ASSET;
    }
}
