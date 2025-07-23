<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\Claims\Concerns\HasScope;

class AssetService extends Claim
{
    use HasScope;

    public static function key(): string
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
