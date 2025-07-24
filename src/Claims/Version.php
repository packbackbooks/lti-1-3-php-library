<?php

namespace Packback\Lti1p3\Claims;

/**
 * Version Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti/claim/version
 *
 * Example payload:
 * {
 *     "https://purl.imsglobal.org/spec/lti/claim/version": "1.3.0"
 * }
 */
class Version extends Claim
{
    public static function claimKey(): string
    {
        return Claim::VERSION;
    }
}
