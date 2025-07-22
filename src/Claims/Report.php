<?php

namespace Packback\Lti1p3\Claims;

class Report extends Claim
{
    public static function key(): string
    {
        return Claim::ASSETREPORT;
    }
}
