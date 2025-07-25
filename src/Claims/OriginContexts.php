<?php

namespace Packback\Lti1p3\Claims;

/**
 * OriginContexts Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti/claim/origin_contexts
 *
 * Example payload:
 * {
 *     "https://purl.imsglobal.org/spec/lti/claim/origin_contexts": [
 *         "7a72828681249ff3a283dfdff8dfb63b05b7a368"
 *     ]
 * }
 */
class OriginContexts extends Claim
{
    public static function claimKey(): string
    {
        return Claim::ORIGIN_CONTEXTS;
    }
}
