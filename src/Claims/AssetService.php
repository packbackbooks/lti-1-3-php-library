<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\Claims\Concerns\HasScope;

/**
 * AssetService Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti/claim/assetservice
 *
 * Example payload:
 * {
 *     "https://purl.imsglobal.org/spec/lti/claim/assetservice": {
 *         "scope": [
 *             "https://purl.imsglobal.org/spec/lti/scope/asset.readonly"
 *         ],
 *         "assets": [
 *             {
 *                 "asset_id": "ce19deb3-4ce6-41d7-92a4-1fda30671d10",
 *                 "url": "https://canvas.localhost/api/lti/asset_processors/1/assets/ce19deb3-4ce6-41d7-92a4-1fda30671d10",
 *                 "sha256_checksum": "99b6378dfbfde920f6224468434af2704d2c491316268caf684fc3bcee9bba2d",
 *                 "timestamp": "2025-06-11T15:22:50Z",
 *                 "size": 321457,
 *                 "content_type": "application/pdf",
 *                 "title": "Pre-Existing Assignment",
 *                 "filename": "Packback Platform Architecture and Data Flow Diagram.pdf"
 *             }
 *         ]
 *     }
 * }
 */
class AssetService extends Claim
{
    use HasScope;

    public static function claimKey(): string
    {
        return Claim::ASSETSERVICE;
    }

    public function assets(): array
    {
        return $this->getBody()['assets'];
    }

    public function scope(): array
    {
        return $this->getBody()['scope'];
    }
}
