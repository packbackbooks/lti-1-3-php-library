<?php

namespace Packback\Lti1p3\Claims;

class AssetReportType extends Claim
{
    public static function key(): string
    {
        return Claim::ASSETREPORT_TYPE;
    }
}
