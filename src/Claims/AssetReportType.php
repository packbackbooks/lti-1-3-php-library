<?php

namespace Packback\Lti1p3\Claims;

/**
 * AssetReportType Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti/claim/assetreport_type
 *
 * Example payload:
 * {
 *     "https://purl.imsglobal.org/spec/lti/claim/assetreport_type": "originality"
 * }
 */
class AssetReportType extends Claim
{
    public static function claimKey(): string
    {
        return Claim::ASSETREPORT_TYPE;
    }
}
