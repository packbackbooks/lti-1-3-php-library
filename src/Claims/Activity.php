<?php

namespace Packback\Lti1p3\Claims;

class Activity extends Claim
{
    public function __construct(
        public string $id
    ) {}

    public static function key(): string
    {
        return LtiConstants::AP_CLAIM_ACTIVITY;
    }

    public function getArray(): array
    {
        return [];
    }
}
