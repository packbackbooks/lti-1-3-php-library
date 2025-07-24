<?php

namespace Packback\Lti1p3\Claims;

/**
 * OriginContexts Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti/claim/origin_contexts
 *
 * No example found in test data.
 */
class OriginContexts extends Claim
{
    public static function claimKey(): string
    {
        return Claim::ORIGIN_CONTEXTS;
    }
}
