<?php

namespace Tests\DeepLinkResources;

use Packback\Lti1p3\DeepLinkResources\Icon;
use Tests\TestCase;

class IconTest extends TestCase
{
    private $imageUrl;
    private $icon;
    protected function setUp(): void
    {
        $this->imageUrl = 'https://example.com/image.png';
        $this->icon = new Icon($this->imageUrl, 1, 2);
    }

    public function test_it_instantiates()
    {
        $this->assertInstanceOf(Icon::class, $this->icon);
    }

    public function test_it_creates_a_new_instance()
    {
        $DeepLinkResources = Icon::new($this->imageUrl, 100, 200);

        $this->assertInstanceOf(Icon::class, $DeepLinkResources);
    }

    public function test_it_gets_url()
    {
        $result = $this->icon->getUrl();

        $this->assertEquals($this->imageUrl, $result);
    }

    public function test_it_sets_url()
    {
        $expected = 'expected';

        $this->icon->setUrl($expected);

        $this->assertEquals($expected, $this->icon->getUrl());
    }

    public function test_it_gets_width()
    {
        $result = $this->icon->getWidth();

        $this->assertEquals(1, $result);
    }

    public function test_it_sets_width()
    {
        $expected = 300;

        $this->icon->setWidth($expected);

        $this->assertEquals($expected, $this->icon->getWidth());
    }

    public function test_it_gets_height()
    {
        $result = $this->icon->getHeight();

        $this->assertEquals(2, $result);
    }

    public function test_it_sets_height()
    {
        $expected = 400;

        $this->icon->setHeight($expected);

        $this->assertEquals($expected, $this->icon->getHeight());
    }

    public function test_it_creates_array()
    {
        $expected = [
            'url' => $this->imageUrl,
            'width' => 100,
            'height' => 200,
        ];

        $this->icon->setUrl($expected['url']);
        $this->icon->setWidth($expected['width']);
        $this->icon->setHeight($expected['height']);

        $result = $this->icon->toArray();

        $this->assertEquals($expected, $result);
    }
}
