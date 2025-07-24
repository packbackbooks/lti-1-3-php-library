<?php

namespace Tests\Claims;

use Mockery;
use Packback\Lti1p3\Claims\AssetService;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Messages\LtiMessage;
use Tests\TestCase;

class AssetServiceTest extends TestCase
{
    private $messageMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->messageMock = Mockery::mock(LtiMessage::class);
    }

    public function test_claim_key_returns_asset_service_constant()
    {
        $this->assertEquals(Claim::ASSETSERVICE, AssetService::claimKey());
    }

    public function test_constructor_sets_body()
    {
        $body = ['assets' => ['asset-1', 'asset-2'], 'scope' => ['read', 'write']];
        $assetService = new AssetService($body);

        $this->assertEquals($body, $assetService->getBody());
    }

    public function test_create_method_creates_instance_from_message()
    {
        $assetServiceData = ['assets' => ['asset-123'], 'service_version' => '1.0'];
        $messageBody = [Claim::ASSETSERVICE => $assetServiceData];
        $this->messageMock->shouldReceive('getBody')->andReturn($messageBody);

        $assetService = AssetService::create($this->messageMock);

        $this->assertInstanceOf(AssetService::class, $assetService);
        $this->assertEquals($assetServiceData, $assetService->getBody());
    }

    public function test_assets_method_returns_assets_from_body()
    {
        $assets = ['asset-1', 'asset-2', 'asset-3'];
        $body = ['assets' => $assets, 'scope' => ['read']];
        $assetService = new AssetService($body);

        $this->assertEquals($assets, $assetService->assets());
    }

    public function test_scope_method_returns_scope_from_body()
    {
        $scope = ['read', 'write', 'delete'];
        $body = ['assets' => ['asset-1'], 'scope' => $scope];
        $assetService = new AssetService($body);

        $this->assertEquals($scope, $assetService->scope());
    }
}
