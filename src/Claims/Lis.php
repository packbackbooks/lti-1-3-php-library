<?php

namespace Packback\Lti1p3\Claims;

/**
 * Lis Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti/claim/lis
 *
 * No example found in test data.
 */
class Lis extends Claim
{
    public static function claimKey(): string
    {
        return Claim::LIS;
    }
}
