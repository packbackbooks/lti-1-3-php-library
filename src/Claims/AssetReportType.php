<?php

namespace Packback\Lti1p3\Claims;

class AssetReportType extends Claim
{
    public static function claimKey(): string
    {
        return Claim::ASSETREPORT_TYPE;
    }
}
