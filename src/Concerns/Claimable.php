<?php

namespace Packback\Lti1p3\Concerns;

use Packback\Lti1p3\LtiException;

trait Claimable
{
    abstract public static function requiredClaims(): array;

    abstract public function hasClaim(string $claim): bool;

    protected static function universallyRequiredClaims(): array
    {
        return [
            LtiConstants::VERSION,
            LtiConstants::DEPLOYMENT_ID,
            LtiConstants::ROLES,
        ];
    }

    public function validateClaims(): static
    {
        foreach (static::universallyRequiredClaims() as $claim) {
            if (!$this->hasClaim($claim)) {
                // Unable to identify message type.
                throw new LtiException('Missing required claim: '.$claim);
            }
        }

        foreach (static::requiredClaims() as $claim) {
            if (!$this->hasClaim($claim)) {
                // Unable to identify message type.
                throw new LtiException('Missing required claim: '.$claim);
            }
        }

        return $this;
    }
}
