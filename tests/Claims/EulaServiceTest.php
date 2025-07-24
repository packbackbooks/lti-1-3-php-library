<?php

namespace Tests\Claims;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\EulaService;
use Packback\Lti1p3\Messages\LtiMessage;
use Tests\TestCase;

class EulaServiceTest extends TestCase
{
    private $messageMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->messageMock = Mockery::mock(LtiMessage::class);
    }

    public function test_claim_key_returns_eula_service_constant()
    {
        $this->assertEquals(Claim::EULASERVICE, EulaService::claimKey());
    }

    public function test_constructor_sets_body()
    {
        $body = [
            'url' => 'https://example.com/eula',
            'scope' => ['https://purl.imsglobal.org/spec/lti/scope/eula'],
        ];
        $eulaService = new EulaService($body);

        $this->assertEquals($body, $eulaService->getBody());
    }

    public function test_create_method_creates_instance_from_message()
    {
        $eulaServiceData = [
            'url' => 'https://example.com/terms',
            'version' => '1.0',
        ];
        $messageBody = [Claim::EULASERVICE => $eulaServiceData];
        $this->messageMock->shouldReceive('getBody')->andReturn($messageBody);

        $eulaService = EulaService::create($this->messageMock);

        $this->assertInstanceOf(EulaService::class, $eulaService);
        $this->assertEquals($eulaServiceData, $eulaService->getBody());
    }

    public function test_url_method_returns_url_from_body()
    {
        $url = 'https://example.com/eula-agreement';
        $body = ['url' => $url, 'scope' => ['read']];
        $eulaService = new EulaService($body);

        $this->assertEquals($url, $eulaService->url());
    }

    public function test_scope_method_returns_scope_from_body()
    {
        $scope = ['https://purl.imsglobal.org/spec/lti/scope/eula'];
        $body = ['url' => 'https://example.com/eula', 'scope' => $scope];
        $eulaService = new EulaService($body);

        $this->assertEquals($scope, $eulaService->scope());
    }
}
