<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\Claims\Concerns\HasId;

/**
 * Asset Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti/claim/asset
 *
 * Example payload:
 * {
 *     "https://purl.imsglobal.org/spec/lti/claim/asset": {
 *         "id": "d5063b10-eb2d-40c2-bc5c-a4a1d8c49d10"
 *     }
 * }
 */
class Asset extends Claim
{
    use HasId;

    public static function claimKey(): string
    {
        return Claim::ASSET;
    }
}
