<?php

namespace Packback\Lti1p3\Claims;

/**
 * Data Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti-dl/claim/data
 *
 * No example found in test data.
 */
class Data extends Claim
{
    public static function claimKey(): string
    {
        return Claim::DL_DATA;
    }
}
