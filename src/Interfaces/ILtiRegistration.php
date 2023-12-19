<?php

namespace Packback\Lti1p3\Interfaces;

/** @internal */
interface ILtiRegistration
{
    public function getIssuer();

    public function setIssuer(string $issuer): ILtiRegistration;

    public function getClientId();

    public function setClientId(string $clientId): ILtiRegistration;

    public function getKeySetUrl();

    public function setKeySetUrl(string $keySetUrl): ILtiRegistration;

    public function getAuthTokenUrl();

    public function setAuthTokenUrl(string $authTokenUrl): ILtiRegistration;

    public function getAuthLoginUrl();

    public function setAuthLoginUrl(string $authLoginUrl): ILtiRegistration;

    public function getAuthServer();

    public function setAuthServer(string $authServer): ILtiRegistration;

    public function getToolPrivateKey();

    public function setToolPrivateKey(string $toolPrivateKey): ILtiRegistration;

    public function getKid();

    public function setKid(string $kid): ILtiRegistration;
}
