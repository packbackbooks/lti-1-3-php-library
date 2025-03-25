<?php

namespace Tests\Helpers;

use Packback\Lti1p3\Helpers\Helpers;
use Tests\TestCase;

class HelpersTest extends TestCase
{
    public function test_it_builds_a_url_with_no_params()
    {
        $expected = 'https://www.example.com';
        $actual = Helpers::buildUrlWithQueryParams($expected);

        $this->assertEquals($expected, $actual);
    }

    public function test_it_builds_a_url_with_params()
    {
        $baseUrl = 'https://www.example.com';
        $actual = Helpers::buildUrlWithQueryParams($baseUrl, ['foo' => 'bar']);

        $this->assertEquals('https://www.example.com?foo=bar', $actual);
    }

    public function test_it_builds_a_url_with_existing_params()
    {
        $baseUrl = 'https://www.example.com?baz=bat';
        $actual = Helpers::buildUrlWithQueryParams($baseUrl, ['foo' => 'bar']);

        $this->assertEquals('https://www.example.com?baz=bat&foo=bar', $actual);
    }
}
