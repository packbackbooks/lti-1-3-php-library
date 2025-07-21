<?php

namespace Packback\Lti1p3\Concerns;

use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\LtiException;

trait Claimable
{
    // abstract public static function requiredClaims(): array;

    public function getClaim(array $jwt, string $claim): mixed
    {
        return $jwt['body'][$claim];
    }

    public function hasClaim(array $jwt, string $claim): bool
    {
        return isset($jwt['body'][$claim]);
    }

    protected static function universallyRequiredClaims(): array
    {
        return [
            LtiConstants::VERSION,
            LtiConstants::DEPLOYMENT_ID,
            LtiConstants::ROLES,
        ];
    }

    public function validateUniversalClaims(array $jwt): static
    {
        foreach (static::universallyRequiredClaims() as $claim) {
            if (!$this->hasClaim($jwt, $claim)) {
                // Unable to identify message type.
                throw new LtiException('Missing required claim: '.$claim);
            }
        }

        return $this;
    }

    public function validateClaims(array $jwt): static
    {
        $this->validateUniversalClaims($jwt);

        foreach (static::requiredClaims() as $claim) {
            if (!$this->hasClaim($jwt, $claim)) {
                // Unable to identify message type.
                throw new LtiException('Missing required claim: '.$claim);
            }
        }

        return $this;
    }
}
