<?php

namespace Packback\Lti1p3\Claims;

/**
 * ContentItems Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti-dl/claim/content_items
 *
 * No example found in test data.
 */
class ContentItems extends Claim
{
    public static function claimKey(): string
    {
        return Claim::DL_CONTENT_ITEMS;
    }
}
