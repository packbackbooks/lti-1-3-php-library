<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\Claims\Concerns\HasId;

class Context extends Claim
{
    use HasId;

    public static function claimKey(): string
    {
        return Claim::CONTEXT;
    }
}
