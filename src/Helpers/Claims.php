<?php

namespace Packback\Lti1p3\Helpers;

class Claims
{
    public static function getClaimFrom(string $claim, array $jwtBody): mixed
    {
        return $jwtBody[$claim];
    }

    public static function hasClaimInBody(string $claim, array $jwtBody): bool
    {
        return isset($jwtBody[$claim]);
    }
}
