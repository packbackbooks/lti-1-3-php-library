<?php

namespace Packback\Lti1p3\Interfaces;

interface ICache
{
    public function getLaunchData(string $key): ?array;

    public function cacheLaunchData(string $key, array $jwtBody): void;

    public function cacheNonce(string $nonce, string $state): void;

    /**
     * Returns true only if the nonce was cached for this state, and removes it
     * so a captured launch can't be replayed. Make the removal atomic so that
     * only one of two simultaneous launches with the same nonce succeeds.
     */
    public function checkNonceIsValid(string $nonce, string $state): bool;

    public function cacheAccessToken(string $key, string $accessToken): void;

    public function getAccessToken(string $key): ?string;

    public function clearAccessToken(string $key): void;
}
