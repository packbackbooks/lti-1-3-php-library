<?php

namespace Packback\Lti1p3;

use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use GuzzleHttp\Exception\TransferException;
use Packback\Lti1p3\Interfaces\IDatabase;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;

abstract class LtiMessage
{
    public const ERR_FETCH_PUBLIC_KEY = 'Failed to fetch public key.';
    public const ERR_NO_PUBLIC_KEY = 'Unable to find public key.';
    public const ERR_NO_MATCHING_PUBLIC_KEY = 'Unable to find a public key which matches your JWT.';
    public const ERR_MISSING_ID_TOKEN = 'Missing id_token.';
    public const ERR_INVALID_ID_TOKEN = 'Invalid id_token, JWT must contain 3 parts.';
    public const ERR_MISSING_NONCE = 'Missing Nonce.';

    /**
     * :issuerUrl and :clientId are used to substitute the queried issuerUrl
     * and clientId. Do not change those substrings without changing how the
     * error message is built.
     */
    public const ERR_MISSING_REGISTRATION = 'LTI 1.3 Registration not found for Issuer :issuerUrl and Client ID :clientId. Please make sure the LMS has provided the right information, and that the LMS has been registered correctly in the tool.';
    public const ERR_CLIENT_NOT_REGISTERED = 'Client id not registered for this issuer.';
    public const ERR_NO_KID = 'No KID specified in the JWT Header.';
    public const ERR_INVALID_SIGNATURE = 'Invalid signature on id_token';
    public const ERR_MISSING_DEPLOYEMENT_ID = 'No deployment ID was specified';
    public const ERR_MISMATCHED_ALG_KEY = 'The alg specified in the JWT header is incompatible with the JWK key type.';
    protected array $message;
    protected array $jwt;
    protected ?ILtiRegistration $registration;
    protected IDatabase $db;
    protected ILtiServiceConnector $serviceConnector;

    // See https://www.imsglobal.org/spec/security/v1p1#approved-jwt-signing-algorithms.
    protected static $ltiSupportedAlgs = [
        'RS256' => 'RSA',
        'RS384' => 'RSA',
        'RS512' => 'RSA',
        'ES256' => 'EC',
        'ES384' => 'EC',
        'ES512' => 'EC',
    ];

    abstract protected function validateMessage(): static;

    abstract protected function hasJwtToken(): bool;

    abstract protected function getJwtToken(): string;

    /**
     * Fetches the decoded body of the JWT used in the current message.
     */
    public function getBody(): array
    {
        return $this->jwt['body'];
    }

    public static function getMissingRegistrationErrorMsg(string $issuerUrl, ?string $clientId = null): string
    {
        // Guard against client ID being null
        if (!isset($clientId)) {
            $clientId = '(N/A)';
        }

        $search = [':issuerUrl', ':clientId'];
        $replace = [$issuerUrl, $clientId];

        return str_replace($search, $replace, static::ERR_MISSING_REGISTRATION);
    }

    public function setMessage(array $message): static
    {
        $this->message = $message;

        return $this;
    }

    protected function validateJwtFormat(): static
    {
        if (!$this->hasJwtToken()) {
            throw new LtiException(static::ERR_MISSING_ID_TOKEN);
        }

        // Get parts of JWT.
        $jwt_parts = explode('.', $this->getJwtToken());

        if (count($jwt_parts) !== 3) {
            // Invalid number of parts in JWT.
            throw new LtiException(static::ERR_INVALID_ID_TOKEN);
        }

        // Decode JWT headers.
        $this->jwt['header'] = json_decode(JWT::urlsafeB64Decode($jwt_parts[0]), true);
        // Decode JWT Body.
        $this->jwt['body'] = json_decode(JWT::urlsafeB64Decode($jwt_parts[1]), true);

        return $this;
    }

    protected function validateNonce(): static
    {
        if (!isset($this->jwt['body']['nonce'])) {
            throw new LtiException(static::ERR_MISSING_NONCE);
        }

        return $this;
    }

    protected function validateRegistration(): static
    {
        // Find registration.
        $clientId = $this->getAud();
        $issuerUrl = $this->jwt['body']['iss'];
        $this->registration = $this->db->findRegistrationByIssuer($issuerUrl, $clientId);

        if (!isset($this->registration)) {
            throw new LtiException($this->getMissingRegistrationErrorMsg($issuerUrl, $clientId));
        }

        // Check client id.
        if ($clientId !== $this->registration->getClientId()) {
            // Client not registered.
            throw new LtiException(static::ERR_CLIENT_NOT_REGISTERED);
        }

        return $this;
    }

    protected function validateJwtSignature(): static
    {
        if (!isset($this->jwt['header']['kid'])) {
            throw new LtiException(static::ERR_NO_KID);
        }

        // Fetch public key.
        $public_key = $this->getPublicKey();

        // Validate JWT signature
        try {
            $headers = new \stdClass;
            JWT::decode($this->getJwtToken(), $public_key, $headers);
        } catch (ExpiredException $e) {
            // Error validating signature.
            throw new LtiException(static::ERR_INVALID_SIGNATURE, previous: $e);
        }

        return $this;
    }

    protected function validateDeployment(): static
    {
        if (!isset($this->jwt['body'][LtiConstants::DEPLOYMENT_ID])) {
            throw new LtiException(static::ERR_MISSING_DEPLOYEMENT_ID);
        }

        return $this;
    }

    protected function getAud(): string
    {
        if (is_array($this->jwt['body']['aud'])) {
            return $this->jwt['body']['aud'][0];
        } else {
            return $this->jwt['body']['aud'];
        }
    }

    /**
     * @throws LtiException
     */
    private function getPublicKey(): Key
    {
        $request = new ServiceRequest(
            ServiceRequest::METHOD_GET,
            $this->registration->getKeySetUrl(),
            ServiceRequest::TYPE_GET_KEYSET
        );

        // Download key set
        try {
            $response = $this->serviceConnector->makeRequest($request);
        } catch (TransferException $e) {
            throw new LtiException(static::ERR_NO_PUBLIC_KEY, previous: $e);
        }
        $publicKeySet = $this->serviceConnector->getResponseBody($response);

        if (empty($publicKeySet)) {
            // Failed to fetch public keyset from URL.
            throw new LtiException(static::ERR_FETCH_PUBLIC_KEY);
        }

        // Find key used to sign the JWT (matches the KID in the header)
        foreach ($publicKeySet['keys'] as $key) {
            if ($key['kid'] == $this->jwt['header']['kid']) {
                $key['alg'] = $this->getKeyAlgorithm($key);

                try {
                    $keySet = JWK::parseKeySet([
                        'keys' => [$key],
                    ]);
                } catch (Exception $e) {
                    // Do nothing
                }

                if (isset($keySet[$key['kid']])) {
                    return $keySet[$key['kid']];
                }
            }
        }

        // Could not find public key with a matching kid and alg.
        throw new LtiException(static::ERR_NO_MATCHING_PUBLIC_KEY);
    }

    /**
     * If alg is omitted from the JWK, infer it from the JWT header alg.
     * See https://datatracker.ietf.org/doc/html/rfc7517#section-4.4.
     */
    private function getKeyAlgorithm(array $key): string
    {
        if (isset($key['alg'])) {
            return $key['alg'];
        }

        // The header alg must match the key type (family) specified in the JWK's kty.
        if ($this->jwtAlgMatchesJwkKty($key)) {
            return $this->jwt['header']['alg'];
        }

        throw new LtiException(static::ERR_MISMATCHED_ALG_KEY);
    }

    private function jwtAlgMatchesJwkKty(array $key): bool
    {
        $jwtAlg = $this->jwt['header']['alg'];

        return isset(static::$ltiSupportedAlgs[$jwtAlg]) &&
            static::$ltiSupportedAlgs[$jwtAlg] === $key['kty'];
    }
}
