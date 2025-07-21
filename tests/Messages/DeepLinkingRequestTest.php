<?php

namespace Tests\Messages;

use Packback\Lti1p3\Messages\DeepLinkingRequest;
use Tests\TestCase;

class DeepLinkingRequestTest extends TestCase
{
    protected function setUp(): void {}

    public function test_it_creates_new_instance()
    {
        $deepLinkingRequest = new DeepLinkingRequest([]);

        $this->assertInstanceOf(DeepLinkingRequest::class, $deepLinkingRequest);
    }
}
