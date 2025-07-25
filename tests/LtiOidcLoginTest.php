<?php

namespace Tests;

use Mockery;
use Packback\Lti1p3\Interfaces\ICache;
use Packback\Lti1p3\Interfaces\ICookie;
use Packback\Lti1p3\Interfaces\IDatabase;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\LtiMessageLaunch;
use Packback\Lti1p3\LtiOidcLogin;
use Packback\Lti1p3\OidcException;

class LtiOidcLoginTest extends TestCase
{
    private $cache;
    private $cookie;
    private $database;
    private $oidcLogin;
    protected function setUp(): void
    {
        $this->cache = Mockery::mock(ICache::class);
        $this->cookie = Mockery::mock(ICookie::class);
        $this->database = Mockery::mock(IDatabase::class);

        $this->oidcLogin = new LtiOidcLogin(
            $this->database,
            $this->cache,
            $this->cookie
        );
    }

    public function test_it_instantiates()
    {
        $this->assertInstanceOf(LtiOidcLogin::class, $this->oidcLogin);
    }

    public function test_it_creates_a_new_instance()
    {
        $oidcLogin = LtiOidcLogin::new(
            $this->database,
            $this->cache,
            $this->cookie
        );

        $this->assertInstanceOf(LtiOidcLogin::class, $this->oidcLogin);
    }

    public function test_it_validates_a_request()
    {
        $expected = Mockery::mock(ILtiRegistration::class);
        $request = [
            'iss' => 'Issuer',
            'login_hint' => 'LoginHint',
            'client_id' => 'ClientId',
        ];

        $this->database->shouldReceive('findRegistrationByIssuer')
            ->once()->with($request['iss'], $request['client_id'])
            ->andReturn($expected);

        $result = $this->oidcLogin->validateOidcLogin($request);

        $this->assertEquals($expected, $result);
    }

    public function test_validates_fails_if_issuer_is_not_set()
    {
        $request = [
            'login_hint' => 'LoginHint',
            'client_id' => 'ClientId',
        ];

        $this->expectException(OidcException::class);
        $this->expectExceptionMessage(LtiOidcLogin::ERROR_MSG_ISSUER);

        $this->oidcLogin->validateOidcLogin($request);
    }

    public function test_validates_fails_if_login_hint_is_not_set()
    {
        $request = [
            'iss' => 'Issuer',
            'client_id' => 'ClientId',
        ];

        $this->expectException(OidcException::class);
        $this->expectExceptionMessage(LtiOidcLogin::ERROR_MSG_LOGIN_HINT);

        $this->oidcLogin->validateOidcLogin($request);
    }

    /**
     * @runInSeparateProcess
     *
     * @preserveGlobalState disabled
     */
    public function test_validates_fails_if_registration_not_found()
    {
        $request = [
            'iss' => 'Issuer',
            'login_hint' => 'LoginHint',
        ];
        $this->database->shouldReceive('findRegistrationByIssuer')
            ->once()->andReturn(null);

        // Use an alias to mock LtiMessageLaunch::getMissingRegistrationErrorMsg()
        $expectedError = 'Registration not found!';
        Mockery::mock('alias:'.LtiMessageLaunch::class)
            ->shouldReceive('getMissingRegistrationErrorMsg')
            ->andReturn($expectedError);

        $this->expectException(OidcException::class);
        $this->expectExceptionMessage($expectedError);

        $this->oidcLogin->validateOidcLogin($request);
    }

    public function test_get_auth_params()
    {
        $this->cookie->shouldReceive('setCookie')
            ->once();
        $this->cache->shouldReceive('cacheNonce')
            ->once();

        $launchUrl = 'https://example.com/launch';
        $clientId = 'ClientId';
        $expected = [
            'scope' => 'openid',
            'response_type' => 'id_token',
            'response_mode' => 'form_post',
            'prompt' => 'none',
            'client_id' => $clientId,
            'redirect_uri' => $launchUrl,
            'login_hint' => 'LoginHint',
            'lti_message_hint' => 'LtiMessageHint',
        ];
        $request = [
            'login_hint' => 'LoginHint',
            'lti_message_hint' => 'LtiMessageHint',
        ];

        $result = $this->oidcLogin->getAuthParams($launchUrl, $clientId, $request);

        // These are cryptographically random, so just assert they exist
        $this->assertArrayHasKey('state', $result);
        $this->assertArrayHasKey('nonce', $result);
        // No remove them and check equality
        unset($result['state'], $result['nonce']);
        $this->assertEquals($expected, $result);
    }

    public function test_get_redirect_url_builds_correct_auth_url()
    {
        $launchUrl = 'https://example.com/launch';
        $request = [
            'iss' => 'https://platform.example.com',
            'login_hint' => 'user-12345',
            'client_id' => 'test-client-id',
            'lti_message_hint' => 'context-hint-67890',
        ];

        $registrationMock = Mockery::mock(ILtiRegistration::class);
        $registrationMock->shouldReceive('getClientId')
            ->andReturn('test-client-id');
        $registrationMock->shouldReceive('getAuthLoginUrl')
            ->andReturn('https://platform.example.com/auth');

        $this->database->shouldReceive('findRegistrationByIssuer')
            ->with('https://platform.example.com', 'test-client-id')
            ->andReturn($registrationMock);

        $this->cookie->shouldReceive('setCookie')
            ->once();
        $this->cache->shouldReceive('cacheNonce')
            ->once();

        $result = $this->oidcLogin->getRedirectUrl($launchUrl, $request);

        $this->assertStringStartsWith('https://platform.example.com/auth?', $result);
        $this->assertStringContainsString('scope=openid', $result);
        $this->assertStringContainsString('response_type=id_token', $result);
        $this->assertStringContainsString('response_mode=form_post', $result);
        $this->assertStringContainsString('prompt=none', $result);
        $this->assertStringContainsString('client_id=test-client-id', $result);
        $this->assertStringContainsString('redirect_uri='.urlencode($launchUrl), $result);
        $this->assertStringContainsString('login_hint=user-12345', $result);
        $this->assertStringContainsString('lti_message_hint=context-hint-67890', $result);
        $this->assertStringContainsString('state=state-', $result);
        $this->assertStringContainsString('nonce=nonce-', $result);
    }

    public function test_get_redirect_url_without_lti_message_hint()
    {
        $launchUrl = 'https://example.com/launch';
        $request = [
            'iss' => 'https://platform.example.com',
            'login_hint' => 'user-12345',
            'client_id' => 'test-client-id',
        ];

        $registrationMock = Mockery::mock(ILtiRegistration::class);
        $registrationMock->shouldReceive('getClientId')
            ->andReturn('test-client-id');
        $registrationMock->shouldReceive('getAuthLoginUrl')
            ->andReturn('https://platform.example.com/auth');

        $this->database->shouldReceive('findRegistrationByIssuer')
            ->with('https://platform.example.com', 'test-client-id')
            ->andReturn($registrationMock);

        $this->cookie->shouldReceive('setCookie')
            ->once();
        $this->cache->shouldReceive('cacheNonce')
            ->once();

        $result = $this->oidcLogin->getRedirectUrl($launchUrl, $request);

        $this->assertStringStartsWith('https://platform.example.com/auth?', $result);
        $this->assertStringContainsString('login_hint=user-12345', $result);
        $this->assertStringNotContainsString('lti_message_hint', $result);
    }

    public function test_get_redirect_url_throws_exception_for_missing_issuer()
    {
        $launchUrl = 'https://example.com/launch';
        $request = [
            'login_hint' => 'user-12345',
            'client_id' => 'test-client-id',
        ];

        $this->expectException(OidcException::class);
        $this->expectExceptionMessage(LtiOidcLogin::ERROR_MSG_ISSUER);

        $this->oidcLogin->getRedirectUrl($launchUrl, $request);
    }

    public function test_get_redirect_url_throws_exception_for_missing_login_hint()
    {
        $launchUrl = 'https://example.com/launch';
        $request = [
            'iss' => 'https://platform.example.com',
            'client_id' => 'test-client-id',
        ];

        $this->expectException(OidcException::class);
        $this->expectExceptionMessage(LtiOidcLogin::ERROR_MSG_LOGIN_HINT);

        $this->oidcLogin->getRedirectUrl($launchUrl, $request);
    }

    /**
     * @runInSeparateProcess
     *
     * @preserveGlobalState disabled
     */
    public function test_get_redirect_url_throws_exception_for_missing_registration()
    {
        $launchUrl = 'https://example.com/launch';
        $request = [
            'iss' => 'https://platform.example.com',
            'login_hint' => 'user-12345',
            'client_id' => 'test-client-id',
        ];

        $this->database->shouldReceive('findRegistrationByIssuer')
            ->with('https://platform.example.com', 'test-client-id')
            ->andReturn(null);

        $expectedError = 'Registration not found!';
        Mockery::mock('alias:'.LtiMessageLaunch::class)
            ->shouldReceive('getMissingRegistrationErrorMsg')
            ->andReturn($expectedError);

        $this->expectException(OidcException::class);
        $this->expectExceptionMessage($expectedError);

        $this->oidcLogin->getRedirectUrl($launchUrl, $request);
    }

    public function test_get_redirect_url_with_special_characters_in_urls()
    {
        $launchUrl = 'https://example.com/launch?param=value&other=test';
        $request = [
            'iss' => 'https://platform.example.com',
            'login_hint' => 'user-with-special@chars.com',
            'client_id' => 'test-client-id',
            'lti_message_hint' => 'context with spaces and & symbols',
        ];

        $registrationMock = Mockery::mock(ILtiRegistration::class);
        $registrationMock->shouldReceive('getClientId')
            ->andReturn('test-client-id');
        $registrationMock->shouldReceive('getAuthLoginUrl')
            ->andReturn('https://platform.example.com/auth?existing=param');

        $this->database->shouldReceive('findRegistrationByIssuer')
            ->with('https://platform.example.com', 'test-client-id')
            ->andReturn($registrationMock);

        $this->cookie->shouldReceive('setCookie')
            ->once();
        $this->cache->shouldReceive('cacheNonce')
            ->once();

        $result = $this->oidcLogin->getRedirectUrl($launchUrl, $request);

        $this->assertStringStartsWith('https://platform.example.com/auth?', $result);
        $this->assertStringContainsString('existing=param', $result);
        $this->assertStringContainsString('login_hint='.urlencode('user-with-special@chars.com'), $result);
        $this->assertStringContainsString('lti_message_hint='.urlencode('context with spaces and & symbols'), $result);
        $this->assertStringContainsString('redirect_uri='.urlencode($launchUrl), $result);
    }
}
