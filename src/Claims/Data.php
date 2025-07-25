<?php

namespace Packback\Lti1p3\Claims;

/**
 * Data Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti-dl/claim/data
 *
 * Example payload:
 * {
 *     "https://purl.imsglobal.org/spec/lti-dl/claim/data": "csrftoken:c7fbba78-7b75-46e3-9201-11e6d5f36f53"
 * }
 */
class Data extends Claim
{
    public static function claimKey(): string
    {
        return Claim::DL_DATA;
    }
}
