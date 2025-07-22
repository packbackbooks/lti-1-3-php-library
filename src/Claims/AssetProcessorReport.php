<?php

namespace Packback\Lti1p3\Claims;

class AssetProcessorReport extends Claim
{
    public static function key(): string
    {
        return Claim::ASSETREPORT;
    }
}
