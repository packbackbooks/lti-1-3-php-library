<?php

namespace Tests\Messages;

use Packback\Lti1p3\Messages\AssetProcessorSettingsRequest;
use Tests\TestCase;

class AssetProcessorSettingsRequestTest extends TestCase
{
    protected function setUp(): void {}

    public function test_it_creates_new_instance()
    {
        $assetProcessorSettingsRequest = new AssetProcessorSettingsRequest([]);

        $this->assertInstanceOf(AssetProcessorSettingsRequest::class, $assetProcessorSettingsRequest);
    }
}
