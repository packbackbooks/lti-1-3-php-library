<?php

namespace BNSoftware\Lti1p3;

use BNSoftware\Lti1p3\Interfaces\ICache;
use BNSoftware\Lti1p3\Interfaces\ICookie;
use BNSoftware\Lti1p3\Interfaces\IDatabase;
use Exception;

class LtiOidcLogin
{
    public const COOKIE_PREFIX = 'lti1p3_';

    public const ERROR_MSG_LAUNCH_URL = 'No launch URL configured';
    public const ERROR_MSG_ISSUER = 'Could not find issuer';
    public const ERROR_MSG_LOGIN_HINT = 'Could not find login hint';

    private int $stateCookieLifetime = 20;

    private IDatabase $db;
    private ICache $cache;
    private ICookie $cookie;

    /**
     * Constructor.
     *
     * @param IDatabase $database    instance of the database interface used for looking up registrations and
     *                               deployments
     * @param ICache|null $cache     Instance of the Cache interface used to loading and storing launches.
     *                               If none is provided launch data will be store in $_SESSION.
     * @param ICookie|null $cookie   Instance of the Cookie interface used to set and read cookies.
     *                               Will default to using $_COOKIE and setcookie().
     */
    public function __construct(
        IDatabase $database,
        ICache $cache = null,
        ICookie $cookie = null
    ) {
        $this->db = $database;
        $this->cache = $cache;
        $this->cookie = $cookie;
    }

    /**
     * Static function to allow for method chaining without having to assign to a variable first.
     *
     * @param IDatabase    $database
     * @param ICache|null  $cache
     * @param ICookie|null $cookie
     * @return LtiOidcLogin
     */
    public static function new(
        IDatabase $database,
        ICache $cache = null,
        ICookie $cookie = null
    ): LtiOidcLogin {
        return new LtiOidcLogin($database, $cache, $cookie);
    }

    /**
     * Calculate the redirect location to return to based on an OIDC third party initiated login request.
     *
     * @param string     $launch_url   URL to redirect back to after the OIDC login. This URL must match exactly a URL
     *                                 white listed in the platform.
     * @param array|null $request      An array of request parameters. If not set will default to $_REQUEST.
     *
     * @return Redirect returns a redirect object containing the fully formed OIDC login URL
     * @throws OidcException
     */
    public function doOidcLoginRedirect($launch_url, array $request = null): Redirect
    {
        if ($request === null) {
            $request = $_REQUEST;
        }

        if (empty($launch_url)) {
            throw new OidcException(static::ERROR_MSG_LAUNCH_URL, 1);
        }

        // Validate Request Data.
        $registration = $this->validateOidcLogin($request);

        /*
         * Build OIDC Auth Response.
         */

        // Generate State.
        // Set cookie (short lived)
        $state = static::secureRandomString('state-');
        $this->cookie->setCookie(
            static::COOKIE_PREFIX.$state,
            $state,
            $this->stateCookieLifetime
        );

        // Generate Nonce.
        $nonce = static::secureRandomString('nonce-');
        $this->cache->cacheNonce($nonce, $state);

        // Build Response.
        $auth_params = [
            'scope' => 'openid', // OIDC Scope.
            'response_type' => 'id_token', // OIDC response is always an id token.
            'response_mode' => 'form_post', // OIDC response is always a form post.
            'prompt' => 'none', // Don't prompt user on redirect.
            'client_id' => $registration->getClientId(), // Registered client id.
            'redirect_uri' => $launch_url, // URL to return to after login.
            'state' => $state, // State to identify browser session.
            'nonce' => $nonce, // Prevent replay attacks.
            'login_hint' => $request['login_hint'], // Login hint to identify platform session.
        ];

        // Pass back LTI message hint if we have it.
        if (isset($request['lti_message_hint'])) {
            // LTI message hint to identify LTI context within the platform.
            $auth_params['lti_message_hint'] = $request['lti_message_hint'];
        }

        $auth_login_return_url = $registration->getAuthLoginUrl().'?'.http_build_query($auth_params);

        // Return auth redirect.
        return new Redirect($auth_login_return_url, http_build_query($request));
    }

    /**
     * Validate the OIDC login request.
     * @param array $request
     * @return LtiRegistration
     * @throws OidcException
     */
    public function validateOidcLogin(array $request): LtiRegistration
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
            $errorMsg = LtiMessageLaunch::getMissingRegistrationErrorMsg(
                $request['iss'],
                $clientId
            );

            throw new OidcException($errorMsg, 1);
        }

        // Return Registration.
        return $registration;
    }

    /**
     * Generate a secure random string.
     *
     * @param string $prefix
     *
     * @return string
     * @throws Exception
     */
    public static function secureRandomString(string $prefix = ''): string
    {
        return $prefix.hash('sha256', random_bytes(64));
    }

    /**
     * Set the lifetime of the state cookie in seconds.
     *
     * @param int $lifetime
     *
     * @return LtiOidcLogin
     */
    public function setStateCookieLifetime(int $lifetime): self
    {
        $this->stateCookieLifetime = $lifetime;

        return $this;
    }
}
