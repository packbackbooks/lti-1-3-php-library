<?php

namespace Tests\Claims;

use Mockery;
use Packback\Lti1p3\Claims\Asset;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Messages\LtiMessage;
use Tests\TestCase;

class AssetTest extends TestCase
{
    private $messageMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->messageMock = Mockery::mock(LtiMessage::class);
    }

    public function test_claim_key_returns_asset_constant()
    {
        $this->assertEquals(Claim::ASSET, Asset::claimKey());
    }

    public function test_constructor_sets_body()
    {
        $body = ['id' => 'asset-123', 'type' => 'document'];
        $asset = new Asset($body);

        $this->assertEquals($body, $asset->getBody());
    }

    public function test_create_method_creates_instance_from_message()
    {
        $assetData = [
            'id' => 'asset-456',
            'type' => 'file',
            'url' => 'https://example.com/file.pdf',
        ];
        $messageBody = [Claim::ASSET => $assetData];
        $this->messageMock->shouldReceive('getBody')->andReturn($messageBody);

        $asset = Asset::create($this->messageMock);

        $this->assertInstanceOf(Asset::class, $asset);
        $this->assertEquals($assetData, $asset->getBody());
    }

    public function test_id_method_returns_id_from_body()
    {
        $assetId = 'asset-789';
        $body = ['id' => $assetId, 'name' => 'Test Asset'];
        $asset = new Asset($body);

        $this->assertEquals($assetId, $asset->id());
    }
}
