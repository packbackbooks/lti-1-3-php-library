<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\Claims\Concerns\HasScope;

/**
 * AssetReport Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti/claim/assetreport
 *
 * Example payload:
 * {
 *     "https://purl.imsglobal.org/spec/lti/claim/assetreport": {
 *         "scope": [
 *             "https://purl.imsglobal.org/spec/lti/scope/report"
 *         ],
 *         "report_url": "https://canvas.localhost/api/lti/asset_processors/1/reports"
 *     }
 * }
 */
class AssetReport extends Claim
{
    use HasScope;

    public static function claimKey(): string
    {
        return Claim::ASSETREPORT;
    }

    public function reportUrl(): string
    {
        return $this->getBody()['report_url'];
    }
}
