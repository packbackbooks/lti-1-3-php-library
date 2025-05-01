<?php

namespace Tests;

use Packback\Lti1p3\Lti1p1Key;

class Lti1p1KeyTest extends TestCase
{
    private $key;
    protected function setUp(): void
    {
        $this->key = new Lti1p1Key;
    }

    public function test_it_instantiates()
    {
        $this->assertInstanceOf(Lti1p1Key::class, $this->key);
    }

    public function test_it_gets_key()
    {
        $result = $this->key->getKey();

        $this->assertNull($result);
    }

    public function test_it_sets_key()
    {
        $expected = 'expected';

        $this->key->setKey($expected);

        $this->assertEquals($expected, $this->key->getKey());
    }

    public function test_it_gets_secret()
    {
        $result = $this->key->getSecret();

        $this->assertNull($result);
    }

    public function test_it_sets_secret()
    {
        $expected = 'expected';

        $this->key->setSecret($expected);

        $this->assertEquals($expected, $this->key->getSecret());
    }

    public function test_it_signs()
    {
        $key = new Lti1p1Key([
            'key' => 'foo',
            'secret' => 'bar',
        ]);

        $actual = $key->sign('deploymentId', 'iss', 'clientId', 'exp', 'nonce');

        $this->assertEquals('1Ze6akG0koOVeizCVBIyQHJ78Eo3vGUXyqOM0iDqS0k=', $actual);
    }
}
