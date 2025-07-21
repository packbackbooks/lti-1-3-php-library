<?php

namespace Tests\Messages;

use Packback\Lti1p3\Messages\Notice;
use Tests\TestCase;

class NoticeTest extends TestCase
{
    protected function setUp(): void {}

    public function test_it_creates_new_instance()
    {
        $notice = new Notice([]);
        $this->assertInstanceOf(Notice::class, $notice);
    }
}
