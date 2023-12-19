<?php

namespace Tests\DeepLinkResources;

use Packback\Lti1p3\DeepLinkResources\Iframe;
use Tests\TestCase;

class IframeTest extends TestCase
{
    public const INITIAL_SRC = 'https://example.com';
    public const INITIAL_WIDTH = 1;
    public const INITIAL_HEIGHT = 2;
    private Iframe $iframe;

    public function setUp(): void
    {
        $this->iframe = new Iframe(
            self::INITIAL_SRC,
            self::INITIAL_WIDTH,
            self::INITIAL_HEIGHT
        );
    }

    public function testItInstantiates()
    {
        $this->assertInstanceOf(Iframe::class, $this->iframe);
    }

    public function testItCreatesANewInstance()
    {
        $DeepLinkResources = Iframe::new();

        $this->assertInstanceOf(Iframe::class, $DeepLinkResources);
    }

    public function testItGetsWidth()
    {
        $result = $this->iframe->getWidth();

        $this->assertEquals(self::INITIAL_WIDTH, $result);
    }

    public function testItSetsWidth()
    {
        $expected = 300;

        $result = $this->iframe->setWidth($expected);

        $this->assertSame($this->iframe, $result);
        $this->assertEquals($expected, $this->iframe->getWidth());
    }

    public function testItGetsHeight()
    {
        $result = $this->iframe->getHeight();

        $this->assertEquals(self::INITIAL_HEIGHT, $result);
    }

    public function testItSetsHeight()
    {
        $expected = 400;

        $result = $this->iframe->setHeight($expected);

        $this->assertSame($this->iframe, $result);
        $this->assertEquals($expected, $this->iframe->getHeight());
    }

    public function testItGetsSrc()
    {
        $result = $this->iframe->getSrc();

        $this->assertEquals(self::INITIAL_SRC, $result);
    }

    public function testItSetsSrc()
    {
        $expected = 'https://example.com/foo/bar';

        $result = $this->iframe->setSrc($expected);

        $this->assertSame($this->iframe, $result);
        $this->assertEquals($expected, $this->iframe->getSrc());
    }

    public function testItCreatesArrayWithoutOptionalProperties()
    {
        $this->iframe->setWidth(null);
        $this->iframe->setHeight(null);
        $this->iframe->setSrc(null);

        $result = $this->iframe->toArray();

        $this->assertEquals([], $result);
    }

    public function testItCreatesArrayWithDefinedOptionalProperties()
    {
        $expected = [
            'width' => 100,
            'height' => 200,
            'src' => 'https://example.com/foo/bar',
        ];

        $this->iframe->setWidth($expected['width']);
        $this->iframe->setHeight($expected['height']);
        $this->iframe->setSrc($expected['src']);

        $result = $this->iframe->toArray();

        $this->assertEquals($expected, $result);
    }
}
