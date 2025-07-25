<?php

namespace Packback\Lti1p3\Claims;

/**
 * Custom Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti/claim/custom
 *
 * Example payload:
 * {
 *     "https://purl.imsglobal.org/spec/lti/claim/custom": {
 *         "some_setting": "az-123"
 *     }
 * }
 */
class Custom extends Claim
{
    public static function claimKey(): string
    {
        return Claim::CUSTOM;
    }
}
