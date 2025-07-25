<?php

namespace Tests;

use Packback\Lti1p3\LtiRegistration;

class LtiRegistrationTest extends TestCase
{
    private $registration;
    protected function setUp(): void
    {
        $this->registration = new LtiRegistration;
    }

    public function test_it_instantiates()
    {
        $this->assertInstanceOf(LtiRegistration::class, $this->registration);
    }

    public function test_it_creates_a_new_instance()
    {
        $registration = LtiRegistration::new();

        $this->assertInstanceOf(LtiRegistration::class, $registration);
    }

    public function test_it_gets_issuer()
    {
        $expected = 'expected';
        $registration = new LtiRegistration(['issuer' => $expected]);

        $result = $registration->getIssuer();

        $this->assertEquals($expected, $result);
    }

    public function test_it_sets_issuer()
    {
        $expected = 'expected';

        $this->registration->setIssuer($expected);

        $this->assertEquals($expected, $this->registration->getIssuer());
    }

    public function test_it_gets_client_id()
    {
        $expected = 'expected';
        $registration = new LtiRegistration(['clientId' => $expected]);

        $result = $registration->getClientId();

        $this->assertEquals($expected, $result);
    }

    public function test_it_sets_client_id()
    {
        $expected = 'expected';

        $this->registration->setClientId($expected);

        $this->assertEquals($expected, $this->registration->getClientId());
    }

    public function test_it_gets_key_set_url()
    {
        $expected = 'expected';
        $registration = new LtiRegistration(['keySetUrl' => $expected]);

        $result = $registration->getKeySetUrl();

        $this->assertEquals($expected, $result);
    }

    public function test_it_sets_key_set_url()
    {
        $expected = 'expected';

        $this->registration->setKeySetUrl($expected);

        $this->assertEquals($expected, $this->registration->getKeySetUrl());
    }

    public function test_it_gets_auth_token_url()
    {
        $expected = 'expected';
        $registration = new LtiRegistration(['authTokenUrl' => $expected]);

        $result = $registration->getAuthTokenUrl();

        $this->assertEquals($expected, $result);
    }

    public function test_it_sets_auth_token_url()
    {
        $expected = 'expected';

        $this->registration->setAuthTokenUrl($expected);

        $this->assertEquals($expected, $this->registration->getAuthTokenUrl());
    }

    public function test_it_gets_auth_login_url()
    {
        $expected = 'expected';
        $registration = new LtiRegistration(['authLoginUrl' => $expected]);

        $result = $registration->getAuthLoginUrl();

        $this->assertEquals($expected, $result);
    }

    public function test_it_sets_auth_login_url()
    {
        $expected = 'expected';

        $this->registration->setAuthLoginUrl($expected);

        $this->assertEquals($expected, $this->registration->getAuthLoginUrl());
    }

    public function test_it_gets_auth_server()
    {
        $expected = 'expected';
        $registration = new LtiRegistration(['authServer' => $expected]);

        $result = $registration->getAuthServer();

        $this->assertEquals($expected, $result);
    }

    public function test_it_sets_auth_server()
    {
        $expected = 'expected';

        $this->registration->setAuthServer($expected);

        $this->assertEquals($expected, $this->registration->getAuthServer());
    }

    public function test_it_gets_tool_private_key()
    {
        $expected = 'expected';
        $registration = new LtiRegistration(['toolPrivateKey' => $expected]);

        $result = $registration->getToolPrivateKey();

        $this->assertEquals($expected, $result);
    }

    public function test_it_sets_tool_private_key()
    {
        $expected = 'expected';

        $this->registration->setToolPrivateKey($expected);

        $this->assertEquals($expected, $this->registration->getToolPrivateKey());
    }

    public function test_it_gets_kid()
    {
        $expected = 'expected';
        $registration = new LtiRegistration(['kid' => $expected]);

        $result = $registration->getKid();

        $this->assertEquals($expected, $result);
    }

    public function test_it_gets_kid_from_issuer_and_client_id()
    {
        $expected = '39e02c46a08382b7b352b4f1a9d38698b8fe7c8eb74ead609c804b25eeb1db52';
        $registration = new LtiRegistration([
            'issuer' => 'Issuer',
            'client_id' => 'ClientId',
        ]);

        $result = $registration->getKid();

        $this->assertEquals($expected, $result);
    }

    public function test_it_sets_kid()
    {
        $expected = 'expected';

        $this->registration->setKid($expected);

        $this->assertEquals($expected, $this->registration->getKid());
    }
}
