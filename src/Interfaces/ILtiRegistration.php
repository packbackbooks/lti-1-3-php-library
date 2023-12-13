<?php

namespace Packback\Lti1p3\Interfaces;

/** @internal */
interface ILtiRegistration
{
    public function getIssuer();

    public function setIssuer(string $issuer);

    public function getClientId();

    public function setClientId(string $clientId);

    public function getKeySetUrl();

    public function setKeySetUrl(string $keySetUrl);

    public function getAuthTokenUrl();

    public function setAuthTokenUrl(string $authTokenUrl);

    public function getAuthLoginUrl();

    public function setAuthLoginUrl(string $authLoginUrl);

    public function getAuthServer();

    public function setAuthServer(string $authServer);

    public function getToolPrivateKey();

    public function setToolPrivateKey($toolPrivateKey);

    public function getKid();

    public function setKid(string $kid);
}
