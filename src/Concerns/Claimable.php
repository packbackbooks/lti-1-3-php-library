<?php

namespace Packback\Lti1p3\Concerns;

trait Claimable
{
    // abstract public static function requiredClaims(): array;

    public static function getClaimFrom(string $claim, array $jwtBody): mixed
    {
        return $jwtBody[$claim];
    }

    public static function hasClaimInBody(string $claim, array $jwtBody): bool
    {
        return isset($jwtBody[$claim]);
    }
}
