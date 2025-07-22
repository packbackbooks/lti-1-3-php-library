<?php

namespace Packback\Lti1p3\Claims;

class AssetProcessorService extends Claim
{
    public static function key(): string
    {
        return Claim::ASSETSERVICE;
    }
}
