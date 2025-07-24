<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\Claims\Concerns\HasScope;

/**
 * AssetService Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti/claim/assetservice
 *
 * No example found in test data.
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
