<?php

namespace Packback\Lti1p3\Claims;

class Report extends Claim
{
    public static function claimKey(): string
    {
        return Claim::ASSETREPORT;
    }
}
