<?php

namespace Tests\Messages;

use Packback\Lti1p3\Messages\ResourceLinkRequest;
use Tests\TestCase;

class ResourceLinkRequestTest extends TestCase
{
    protected function setUp(): void {}

    public function test_it_creates_new_instance()
    {
        $resourceLinkRequest = new ResourceLinkRequest([]);

        $this->assertInstanceOf(ResourceLinkRequest::class, $resourceLinkRequest);
    }
}
