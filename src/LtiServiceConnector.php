<?php

namespace Packback\Lti1p3;

use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use Packback\Lti1p3\Interfaces\ICache;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;

class LtiServiceConnector implements ILtiServiceConnector
{
    const NEXT_PAGE_REGEX = '/<([^>]*)>; ?rel="next"/i';

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    const CONTENT_TYPE_JSON = 'application/json';
    const CONTENT_TYPE_SCORE = 'application/vnd.ims.lis.v1.score+json';
    const CONTENT_TYPE_LINEITEM = 'application/vnd.ims.lis.v2.lineitem+json';
    const CONTENT_TYPE_RESULTCONTAINER = 'application/vnd.ims.lis.v2.resultcontainer+json';
    const CONTENT_TYPE_CONTEXTGROUPCONTAINER = 'application/vnd.ims.lti-gs.v1.contextgroupcontainer+json';
    const CONTENT_TYPE_MEMBERSHIPCONTAINER = 'application/vnd.ims.lti-nrps.v2.membershipcontainer+json';

    private $cache;
    private $client;
    private $registration;
    private $access_tokens = [];

    public function __construct(ILtiRegistration $registration, ICache $cache, Client $client)
    {
        $this->registration = $registration;
        $this->cache = $cache;
        $this->client = $client;
    }

    public function getAccessToken(array $scopes)
    {
        // Get a unique cache key for the access token
        $accessTokenKey = $this->getAccessTokenCacheKey($scopes);
        // Get access token from cache if it exists
        $accessToken = $this->cache->getAccessToken($accessTokenKey);
        if ($accessToken) {
            return $accessToken;
        }

        // Build up JWT to exchange for an auth token
        $jwtClaim = [
            'iss' => $this->registration->getClientId(),
            'sub' => $this->registration->getClientId(),
            'aud' => $this->registration->getAuthServer(),
            'iat' => time() - 5,
            'exp' => time() + 60,
            'jti' => 'lti-service-token'.hash('sha256', random_bytes(64)),
        ];

        // Sign the JWT with our private key (given by the platform on registration)
        $jwt = JWT::encode($jwtClaim, $this->registration->getToolPrivateKey(), 'RS256', $this->registration->getKid());

        // Build auth token request headers
        $authRequest = [
            'grant_type' => 'client_credentials',
            'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
            'client_assertion' => $jwt,
            'scope' => implode(' ', $scopes),
        ];

        $url = $this->registration->getAuthTokenUrl();

        // Get Access
        $response = $this->client->post($url, [
            'form_params' => $authRequest,
        ]);

        $body = (string) $response->getBody();
        $tokenData = json_decode($body, true);

        // Cache access token
        $this->cache->cacheAccessToken($accessTokenKey, $tokenData['access_token']);

        return $tokenData['access_token'];
    }

    public function post(string $url, string $params, array $scopes, string $contentType)
    {
        $payload = [
            'headers' => [
                'Authorization' => 'Bearer '.$this->getAccessToken($scopes),
                'Accept' => static::CONTENT_TYPE_JSON,
                'Content-Type' => $contentType,
            ],
            'body' => $params,
        ];

        return $this->makeServiceRequest(static::METHOD_POST, $url, $payload);
    }

    public function get(string $url, array $scopes, string $contentType)
    {
        $payload = [
            'headers' => [
                'Authorization' => 'Bearer '.$this->getAccessToken($scopes),
                'Accept' => $contentType,
            ],
        ];

        return $this->makeServiceRequest(static::METHOD_GET, $url, $payload);
    }

    public function getAll(string $url, array $scopes, string $contentType)
    {
        $collection = [];

        $nextUrl = $url;

        while ($nextUrl) {
            $response = $this->get(
                $url,
                $this->service_data['scope'],
                LtiServiceConnector::CONTENT_TYPE_CONTEXTGROUPCONTAINER,
            );

            $collection = array_merge($collection, $response['body']);

            $nextUrl = $this->getNextUrl($response['headers']);
        }

        return $collection;
    }

    private function makeServiceRequest(string $method, string $url, array $payload)
    {
        $response = $this->client->request($method, $url, $payload);

        $respHeaders = $response->getHeaders();
        array_walk($respHeaders, function (&$value) {
            $value = $value[0];
        });
        $respBody = $response->getBody();

        return [
            'headers' => $respHeaders,
            'body' => json_decode($respBody, true),
        ];
    }

    private function getAccessTokenCacheKey(array $scopes)
    {
        sort($scopes);
        $scopeKey = md5(implode('|', $scopes));

        return $this->registration->getIssuer().$this->registration->getClientId().$scopeKey;
    }

    private function getNextUrl(array $headers)
    {
        $subject = $response['headers']['Link'] ?? '';
        preg_match(LtiServiceConnector::NEXT_PAGE_REGEX, $subject, $matches);

        return $matches[1] ?? null;
    }
}
