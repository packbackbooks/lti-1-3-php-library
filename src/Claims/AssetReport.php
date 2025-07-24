<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\Claims\Concerns\HasScope;

/**
 * AssetReport Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti/claim/assetreport
 *
 * No example found in test data.
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
