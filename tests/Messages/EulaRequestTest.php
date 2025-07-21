<?php

namespace Tests\Messages;

use Packback\Lti1p3\Messages\EulaRequest;
use Tests\TestCase;

class EulaRequestTest extends TestCase
{
    protected function setUp(): void {}

    public function test_it_creates_new_instance()
    {
        $eulaRequest = new EulaRequest([]);

        $this->assertInstanceOf(EulaRequest::class, $eulaRequest);
    }
}
