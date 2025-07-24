<?php

namespace Packback\Lti1p3\Claims;

/**
 * Report Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti/claim/assetreport
 *
 * No example found in test data.
 */
class Report extends Claim
{
    public static function claimKey(): string
    {
        return Claim::ASSETREPORT;
    }
}
