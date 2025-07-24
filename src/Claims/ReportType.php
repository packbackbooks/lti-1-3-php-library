<?php

namespace Packback\Lti1p3\Claims;

/**
 * ReportType Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti/claim/assetreport_type
 *
 * Example payload:
 * {
 *     "https://purl.imsglobal.org/spec/lti/claim/assetreport_type": "originality"
 * }
 */
class ReportType extends Claim
{
    public static function claimKey(): string
    {
        return Claim::ASSETREPORT_TYPE;
    }
}
