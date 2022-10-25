<?php

namespace Tests\ImsStorage;

use BNSoftware\Lti1p3\ImsStorage\ImsCache;
use Tests\TestCase;

class ImsCacheTest extends TestCase
{
    public function testItInstantiates()
    {
        $cache = new ImsCache();

        $this->assertInstanceOf(ImsCache::class, $cache);
    }
}
