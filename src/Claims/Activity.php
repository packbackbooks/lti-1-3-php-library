<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\Claims\Concerns\HasId;

class Activity extends Claim
{
    use HasId;

    public static function claimKey(): string
    {
        return Claim::ACTIVITY;
    }
}
