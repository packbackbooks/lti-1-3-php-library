<?php

namespace Packback\Lti1p3\Helpers;

use Packback\Lti1p3\LtiException;

class Claims
{
    public const ERR_INVALID_AUD = 'The aud claim must be a client_id or a list of client_ids.';
    public const ERR_MISSING_AZP = 'The azp claim is required when aud lists more than one client_id.';
    public const ERR_AZP_NOT_IN_AUD = 'The azp claim must be one of the client_ids in aud.';

    public static function getClaimFrom(string $claim, array $jwtBody): mixed
    {
        return $jwtBody[$claim];
    }

    /**
     * Picks the client_id a JWT was issued to: the azp claim when present,
     * otherwise the only audience in aud.
     *
     * @throws LtiException
     */
    public static function getClientIdFrom(array $jwtBody): string
    {
        $audiences = $jwtBody['aud'] ?? null;
        if (is_string($audiences)) {
            $audiences = [$audiences];
        }
        if (!self::isListOfClientIds($audiences)) {
            throw new LtiException(static::ERR_INVALID_AUD);
        }

        if (array_key_exists('azp', $jwtBody)) {
            if (!is_string($jwtBody['azp']) || !in_array($jwtBody['azp'], $audiences, true)) {
                throw new LtiException(static::ERR_AZP_NOT_IN_AUD);
            }

            return $jwtBody['azp'];
        }

        if (count($audiences) > 1) {
            throw new LtiException(static::ERR_MISSING_AZP);
        }

        return $audiences[0];
    }

    public static function hasClaimInBody(string $claim, array $jwtBody): bool
    {
        return isset($jwtBody[$claim]);
    }

    private static function isListOfClientIds(mixed $audiences): bool
    {
        if (!is_array($audiences) || $audiences === [] || !array_is_list($audiences)) {
            return false;
        }

        foreach ($audiences as $audience) {
            if (!is_string($audience) || $audience === '') {
                return false;
            }
        }

        return true;
    }
}
