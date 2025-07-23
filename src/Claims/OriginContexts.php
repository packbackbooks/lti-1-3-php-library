<?php

namespace Packback\Lti1p3\Claims;

class OriginContexts extends Claim
{
    public static function claimKey(): string
    {
        return Claim::ORIGIN_CONTEXTS;
    }
}
