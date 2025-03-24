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

    protected function setUp(): void
    {
        $this->iframe = new Iframe(
            self::INITIAL_SRC,
            self::INITIAL_WIDTH,
            self::INITIAL_HEIGHT
        );
    }

    public function test_it_instantiates()
    {
        $this->assertInstanceOf(Iframe::class, $this->iframe);
    }

    public function test_it_creates_a_new_instance()
    {
        $DeepLinkResources = Iframe::new();

        $this->assertInstanceOf(Iframe::class, $DeepLinkResources);
    }

    public function test_it_gets_width()
    {
        $result = $this->iframe->getWidth();

        $this->assertEquals(self::INITIAL_WIDTH, $result);
    }

    public function test_it_sets_width()
    {
        $expected = 300;

        $result = $this->iframe->setWidth($expected);

        $this->assertSame($this->iframe, $result);
        $this->assertEquals($expected, $this->iframe->getWidth());
    }

    public function test_it_gets_height()
    {
        $result = $this->iframe->getHeight();

        $this->assertEquals(self::INITIAL_HEIGHT, $result);
    }

    public function test_it_sets_height()
    {
        $expected = 400;

        $result = $this->iframe->setHeight($expected);

        $this->assertSame($this->iframe, $result);
        $this->assertEquals($expected, $this->iframe->getHeight());
    }

    public function test_it_gets_src()
    {
        $result = $this->iframe->getSrc();

        $this->assertEquals(self::INITIAL_SRC, $result);
    }

    public function test_it_sets_src()
    {
        $expected = 'https://example.com/foo/bar';

        $result = $this->iframe->setSrc($expected);

        $this->assertSame($this->iframe, $result);
        $this->assertEquals($expected, $this->iframe->getSrc());
    }

    public function test_it_creates_array_without_optional_properties()
    {
        $this->iframe->setWidth(null);
        $this->iframe->setHeight(null);
        $this->iframe->setSrc(null);

        $result = $this->iframe->toArray();

        $this->assertEquals([], $result);
    }

    public function test_it_creates_array_with_defined_optional_properties()
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
