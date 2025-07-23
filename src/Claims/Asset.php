<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\Claims\Concerns\HasId;

class Asset extends Claim
{
    use HasId;

    public static function key(): string
    {
        return Claim::ASSET;
    }
}
