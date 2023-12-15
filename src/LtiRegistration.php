<?php

namespace Packback\Lti1p3;

use Packback\Lti1p3\Interfaces\ILtiRegistration;

class LtiRegistration implements ILtiRegistration
{
    private ?string $issuer;
    private ?string $clientId;
    private ?string $keySetUrl;
    private ?string $authTokenUrl;
    private ?string $authLoginUrl;
    private ?string $authServer;
    private $toolPrivateKey;
    private ?string $kid;

    public function __construct(?array $registration = null)
    {
        $this->issuer = $registration['issuer'] ?? null;
        $this->clientId = $registration['clientId'] ?? null;
        $this->keySetUrl = $registration['keySetUrl'] ?? null;
        $this->authTokenUrl = $registration['authTokenUrl'] ?? null;
        $this->authLoginUrl = $registration['authLoginUrl'] ?? null;
        $this->authServer = $registration['authServer'] ?? null;
        $this->toolPrivateKey = $registration['toolPrivateKey'] ?? null;
        $this->kid = $registration['kid'] ?? null;
    }

    public static function new(?array $registration = null): self
    {
        return new LtiRegistration($registration);
    }

    public function getIssuer()
    {
        return $this->issuer;
    }

    public function setIssuer($issuer): self
    {
        $this->issuer = $issuer;

        return $this;
    }

    public function getClientId()
    {
        return $this->clientId;
    }

    public function setClientId($clientId): self
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function getKeySetUrl()
    {
        return $this->keySetUrl;
    }

    public function setKeySetUrl($keySetUrl): self
    {
        $this->keySetUrl = $keySetUrl;

        return $this;
    }

    public function getAuthTokenUrl()
    {
        return $this->authTokenUrl;
    }

    public function setAuthTokenUrl($authTokenUrl): self
    {
        $this->authTokenUrl = $authTokenUrl;

        return $this;
    }

    public function getAuthLoginUrl()
    {
        return $this->authLoginUrl;
    }

    public function setAuthLoginUrl($authLoginUrl): self
    {
        $this->authLoginUrl = $authLoginUrl;

        return $this;
    }

    public function getAuthServer()
    {
        return $this->authServer ?? $this->authTokenUrl;
    }

    public function setAuthServer($authServer): self
    {
        $this->authServer = $authServer;

        return $this;
    }

    public function getToolPrivateKey()
    {
        return $this->toolPrivateKey;
    }

    public function setToolPrivateKey($toolPrivateKey): self
    {
        $this->toolPrivateKey = $toolPrivateKey;

        return $this;
    }

    public function getKid()
    {
        return $this->kid ?? hash('sha256', trim($this->issuer.$this->clientId));
    }

    public function setKid($kid): self
    {
        $this->kid = $kid;

        return $this;
    }
}
