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
    public function setUp(): void
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

    public function testItInstantiates()
    {
        $this->assertInstanceOf(LtiOidcLogin::class, $this->oidcLogin);
    }

    public function testItCreatesANewInstance()
    {
        $oidcLogin = LtiOidcLogin::new(
            $this->database,
            $this->cache,
            $this->cookie
        );

        $this->assertInstanceOf(LtiOidcLogin::class, $this->oidcLogin);
    }

    public function testItValidatesARequest()
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

    public function testValidatesFailsIfIssuerIsNotSet()
    {
        $request = [
            'login_hint' => 'LoginHint',
            'client_id' => 'ClientId',
        ];

        $this->expectException(OidcException::class);
        $this->expectExceptionMessage(LtiOidcLogin::ERROR_MSG_ISSUER);

        $this->oidcLogin->validateOidcLogin($request);
    }

    public function testValidatesFailsIfLoginHintIsNotSet()
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
    public function testValidatesFailsIfRegistrationNotFound()
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

    public function testGetAuthParams()
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
}
