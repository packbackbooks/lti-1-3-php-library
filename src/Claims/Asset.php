<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\Claims\Concerns\HasErrors;
use Packback\Lti1p3\Claims\Concerns\HasId;

/**
 * Asset Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti/claim/asset
 *
 * Example payload:
 * {
 *     "https://purl.imsglobal.org/spec/lti/claim/asset": {
 *         "id": "c61dcc8e-6b5f-45ec-8205-bbf39f3d8b49",
 *         "validation_context": null,
 *         "errors": {
 *             "errors": {}
 *         }
 *     }
 * }
 */
class Asset extends Claim
{
    use HasErrors;
    use HasId;

    public static function claimKey(): string
    {
        return Claim::ASSET;
    }
}
