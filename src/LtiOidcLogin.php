<?php

namespace Packback\Lti1p3;

use Packback\Lti1p3\Helpers\Helpers;
use Packback\Lti1p3\Interfaces\ICache;
use Packback\Lti1p3\Interfaces\ICookie;
use Packback\Lti1p3\Interfaces\IDatabase;

class LtiOidcLogin
{
    public const COOKIE_PREFIX = 'lti1p3_';
    public const ERROR_MSG_LAUNCH_URL = 'No launch URL configured';
    public const ERROR_MSG_ISSUER = 'Could not find issuer';
    public const ERROR_MSG_LOGIN_HINT = 'Could not find login hint';

    public function __construct(
        private IDatabase $database,
        private ICache $cache,
        private ICookie $cookie
    ) {
    }

    /**
     * Static function to allow for method chaining without having to assign to a variable first.
     */
    public static function new(IDatabase $database, ICache $cache, ICookie $cookie): LtiOidcLogin
    {
        return new LtiOidcLogin($database, $cache, $cookie);
    }

    /**
     * Calculate the redirect location to return to based on an OIDC third party initiated login request.
     *
     * @param  string  $launchUrl URL to redirect back to after the OIDC login. This URL must match exactly a URL white listed in the platform.
     * @param  array  $request    An array of request parameters.
     * @return string returns the fully formed OIDC login URL
     */
    public function getRedirectUrl(string $launchUrl, array $request): string
    {
        // Validate Request Data.
        $registration = $this->validateOidcLogin($request);

        /*
         * Build OIDC Auth Response.
         */

        // Generate State.
        // Set cookie (short lived)
        $state = static::secureRandomString('state-');
        $this->cookie->setCookie(static::COOKIE_PREFIX.$state, $state, 60);

        // Generate Nonce.
        $nonce = static::secureRandomString('nonce-');
        $this->cache->cacheNonce($nonce, $state);

        // Build Response.
        $authParams = [
            'scope' => 'openid', // OIDC Scope.
            'response_type' => 'id_token', // OIDC response is always an id token.
            'response_mode' => 'form_post', // OIDC response is always a form post.
            'prompt' => 'none', // Don't prompt user on redirect.
            'client_id' => $registration->getClientId(), // Registered client id.
            'redirect_uri' => $launchUrl, // URL to return to after login.
            'state' => $state, // State to identify browser session.
            'nonce' => $nonce, // Prevent replay attacks.
            'login_hint' => $request['login_hint'], // Login hint to identify platform session.
        ];

        // Pass back LTI message hint if we have it.
        if (isset($request['lti_message_hint'])) {
            // LTI message hint to identify LTI context within the platform.
            $authParams['lti_message_hint'] = $request['lti_message_hint'];
        }

        return Helpers::buildUrlWithQueryParams($registration->getAuthLoginUrl(), $authParams);
    }

    public function validateOidcLogin(array $request): ILtiRegistration
    {
        // Validate Issuer.
        if (empty($request['iss'])) {
            throw new OidcException(static::ERROR_MSG_ISSUER, 1);
        }

        // Validate Login Hint.
        if (empty($request['login_hint'])) {
            throw new OidcException(static::ERROR_MSG_LOGIN_HINT, 1);
        }

        // Fetch Registration Details.
        $clientId = $request['client_id'] ?? null;
        $registration = $this->db->findRegistrationByIssuer($request['iss'], $clientId);

        // Check we got something.
        if (empty($registration)) {
            $errorMsg = LtiMessageLaunch::getMissingRegistrationErrorMsg($request['iss'], $clientId);

            throw new OidcException($errorMsg, 1);
        }

        // Return Registration.
        return $registration;
    }

    public static function secureRandomString(string $prefix = ''): string
    {
        return $prefix.hash('sha256', random_bytes(64));
    }
}
