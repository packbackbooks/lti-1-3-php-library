<?php

namespace Tests\DeepLinkResources;

use Packback\Lti1p3\DeepLinkResources\Icon;
use Tests\TestCase;

class IconTest extends TestCase
{
    public function setUp(): void
    {
        $this->imageUrl = 'https://example.com/image.png';
        $this->icon = new Icon($this->imageUrl, 1, 2);
    }

    public function testItInstantiates()
    {
        $this->assertInstanceOf(Icon::class, $this->icon);
    }

    public function testItCreatesANewInstance()
    {
        $DeepLinkResources = Icon::new($this->imageUrl, 100, 200);

        $this->assertInstanceOf(Icon::class, $DeepLinkResources);
    }

    public function testItGetsUrl()
    {
        $result = $this->icon->getUrl();

        $this->assertEquals($this->imageUrl, $result);
    }

    public function testItSetsUrl()
    {
        $expected = 'expected';

        $this->icon->setUrl($expected);

        $this->assertEquals($expected, $this->icon->getUrl());
    }

    public function testItGetsWidth()
    {
        $result = $this->icon->getWidth();

        $this->assertEquals(1, $result);
    }

    public function testItSetsWidth()
    {
        $expected = 300;

        $this->icon->setWidth($expected);

        $this->assertEquals($expected, $this->icon->getWidth());
    }

    public function testItGetsHeight()
    {
        $result = $this->icon->getHeight();

        $this->assertEquals(2, $result);
    }

    public function testItSetsHeight()
    {
        $expected = 400;

        $this->icon->setHeight($expected);

        $this->assertEquals($expected, $this->icon->getHeight());
    }

    public function testItCreatesArray()
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
