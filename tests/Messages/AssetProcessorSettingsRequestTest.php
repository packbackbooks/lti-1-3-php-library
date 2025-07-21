<?php

namespace Tests\Messages;

use Mockery;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Messages\AssetProcessorSettingsRequest;
use Tests\TestCase;

class AssetProcessorSettingsRequestTest extends TestCase
{
    private $registrationMock;

    protected function setUp(): void
    {
        $this->registrationMock = Mockery::mock(ILtiRegistration::class);
    }

    public function test_it_creates_new_instance()
    {
        $assetProcessorSettingsRequest = new AssetProcessorSettingsRequest($this->registrationMock, []);

        $this->assertInstanceOf(AssetProcessorSettingsRequest::class, $assetProcessorSettingsRequest);
    }
}
