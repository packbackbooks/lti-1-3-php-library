<?php

namespace Packback\Lti1p3;

use Packback\Lti1p3\Interfaces\IDatabase;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use phpseclib3\Crypt\RSA;

class JwksEndpoint
{
    public function __construct(private array $keys) {}

    public static function new(array $keys): self
    {
        return new JwksEndpoint($keys);
    }

    public static function fromIssuer(IDatabase $database, string $issuer): self
    {
        $registration = $database->findRegistrationByIssuer($issuer);

        return new JwksEndpoint([$registration->getKid() => $registration->getToolPrivateKey()]);
    }

    public static function fromRegistration(ILtiRegistration $registration): self
    {
        return new JwksEndpoint([$registration->getKid() => $registration->getToolPrivateKey()]);
    }

    public function getPublicJwks(): array
    {
        // phpseclib 4 renamed its namespace from phpseclib3 to phpseclib4; support whichever major is installed
        $rsaClass = class_exists(RSA::class) ? RSA::class : \phpseclib4\Crypt\RSA::class;

        $jwks = [];
        foreach ($this->keys as $kid => $private_key) {
            $key = $rsaClass::load($private_key);
            $jwk = json_decode($key->getPublicKey()->toString('JWK'), true);
            $jwks[] = array_merge($jwk['keys'][0], [
                'alg' => 'RS256',
                'use' => 'sig',
                'kid' => $kid,
            ]);
        }

        return ['keys' => $jwks];
    }
}
