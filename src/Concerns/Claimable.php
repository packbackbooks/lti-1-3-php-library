<?php

namespace Packback\Lti1p3\Concerns;

use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\LtiException;

trait Claimable
{
    // abstract public static function requiredClaims(): array;

    public static function getClaimFrom(string $claim, array $jwtBody): mixed
    {
        return $jwtBody[$claim];
    }

    public static function hasClaimInBody(string $claim, array $jwtBody): bool
    {
        return isset($jwtBody[$claim]);
    }

    /**
     * @todo get rid of all of these
     */
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
