<?php

namespace Packback\Lti1p3\Interfaces;

interface ICache
{
    public function getLaunchData(string $key): ?array;

    public function cacheLaunchData(string $key, array $jwtBody): void;

    public function cacheNonce(string $nonce, string $state): void;

    /**
     * Returns true only if the nonce was cached for this state, and removes it
     * so each OIDC login allows one launch. Make the removal atomic so this
     * holds when two requests arrive together.
     */
    public function checkNonceIsValid(string $nonce, string $state): bool;

    public function cacheAccessToken(string $key, string $accessToken): void;

    public function getAccessToken(string $key): ?string;

    public function clearAccessToken(string $key): void;
}
