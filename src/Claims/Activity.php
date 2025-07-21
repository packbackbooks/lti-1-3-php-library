<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\LtiConstants;

class Activity extends Claim
{
    public function __construct(
        public string $id
    ) {}

    public static function key(): string
    {
        return LtiConstants::AP_CLAIM_ACTIVITY;
    }
}
