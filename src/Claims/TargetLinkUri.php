<?php

namespace Packback\Lti1p3\Claims;

/**
 * TargetLinkUri Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti/claim/target_link_uri
 *
 * Example payload:
 * {
 *     "https://purl.imsglobal.org/spec/lti/claim/target_link_uri": "http://localhost:8080/"
 * }
 */
class TargetLinkUri extends Claim
{
    public static function claimKey(): string
    {
        return Claim::TARGET_LINK_URI;
    }
}
