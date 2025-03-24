<?php

namespace Tests;

use Packback\Lti1p3\ServiceRequest;

class ServiceRequestTest extends TestCase
{
    private $method = ServiceRequest::METHOD_GET;
    private $url = 'https://example.com';
    private $type = ServiceRequest::TYPE_AUTH;
    private $request;

    protected function setUp(): void
    {
        $this->request = new ServiceRequest($this->method, $this->url, $this->type);
    }

    public function test_it_instantiates()
    {
        $this->assertInstanceOf(ServiceRequest::class, $this->request);
    }

    public function test_it_gets_url()
    {
        $result = $this->request->getUrl();

        $this->assertEquals($this->url, $result);
    }

    public function test_it_sets_url()
    {
        $expected = 'http://example.com/foo/bar';

        $this->request->setUrl($expected);

        $this->assertEquals($expected, $this->request->getUrl());
    }

    public function test_it_gets_payload()
    {
        $expected = [
            'headers' => [
                'Accept' => 'application/json',
            ],
        ];

        $this->assertEquals($expected, $this->request->getPayload());
    }

    public function test_it_sets_access_token()
    {
        $expected = [
            'headers' => [
                'Authorization' => 'Bearer foo-bar',
                'Accept' => 'application/json',
            ],
        ];

        $this->request->setAccessToken('foo-bar');

        $this->assertEquals($expected, $this->request->getPayload());
    }

    public function test_it_sets_content_type()
    {
        $expected = [
            'headers' => [
                'Content-Type' => 'foo-bar',
                'Accept' => 'application/json',
            ],
        ];

        $request = new ServiceRequest(ServiceRequest::METHOD_POST, $this->url, $this->type);
        $request->setContentType('foo-bar');

        $this->assertEquals($expected, $request->getPayload());
    }

    public function test_it_sets_body()
    {
        $expected = [
            'headers' => [
                'Accept' => 'application/json',
            ],
            'body' => 'foo-bar',
        ];

        $this->request->setBody('foo-bar');

        $this->assertEquals($expected, $this->request->getPayload());
    }

    public function test_it_gets_mask_response_logs()
    {
        $this->assertFalse($this->request->getMaskResponseLogs());
    }

    public function test_it_sets_mask_response_logs()
    {
        $this->request->setMaskResponseLogs(true);

        $this->assertTrue($this->request->getMaskResponseLogs());
    }

    public function test_it_gets_error_prefix()
    {
        $this->assertEquals('Authenticating:', $this->request->getErrorPrefix());
    }
}
