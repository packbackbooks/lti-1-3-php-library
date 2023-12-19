<?php

namespace Tests\DeepLinkResources;

use Packback\Lti1p3\DeepLinkResources\Window;
use Tests\TestCase;

class WindowTest extends TestCase
{
    public const INITIAL_TARGET_NAME = 'example-name';
    public const INITIAL_WIDTH = 1;
    public const INITIAL_HEIGHT = 2;
    public const INITIAL_WINDOW_FEATURES = 'example-feature=value';
    private Window $window;

    public function setUp(): void
    {
        $this->window = new Window(self::INITIAL_TARGET_NAME,
            self::INITIAL_WIDTH, self::INITIAL_HEIGHT, self::INITIAL_WINDOW_FEATURES);
    }

    public function testItInstantiates()
    {
        $this->assertInstanceOf(Window::class, $this->window);
    }

    public function testItCreatesANewInstance()
    {
        $DeepLinkResources = Window::new();

        $this->assertInstanceOf(Window::class, $DeepLinkResources);
    }

    public function testItGetsTargetName()
    {
        $result = $this->window->getTargetName();

        $this->assertEquals(self::INITIAL_TARGET_NAME, $result);
    }

    public function testItSetsTargetName()
    {
        $expected = 'expected';

        $result = $this->window->setTargetName($expected);

        $this->assertSame($this->window, $result);
        $this->assertEquals($expected, $this->window->getTargetName());
    }

    public function testItGetsWidth()
    {
        $result = $this->window->getWidth();

        $this->assertEquals(self::INITIAL_WIDTH, $result);
    }

    public function testItSetsWidth()
    {
        $expected = 300;

        $result = $this->window->setWidth($expected);

        $this->assertSame($this->window, $result);
        $this->assertEquals($expected, $this->window->getWidth());
    }

    public function testItGetsHeight()
    {
        $result = $this->window->getHeight();

        $this->assertEquals(self::INITIAL_HEIGHT, $result);
    }

    public function testItSetsHeight()
    {
        $expected = 400;

        $result = $this->window->setHeight($expected);

        $this->assertSame($this->window, $result);
        $this->assertEquals($expected, $this->window->getHeight());
    }

    public function testItGetsWindowFeatures()
    {
        $result = $this->window->getWindowFeatures();

        $this->assertEquals(self::INITIAL_WINDOW_FEATURES, $result);
    }

    public function testItSetsWindowFeatures()
    {
        $expected = 'first-feature=value,second-feature';

        $result = $this->window->setWindowFeatures($expected);

        $this->assertSame($this->window, $result);
        $this->assertEquals($expected, $this->window->getWindowFeatures());
    }

    public function testItCreatesArray()
    {
        $expected = [
            'targetName' => 'target-name',
            'width' => 100,
            'height' => 200,
            'windowFeatures' => 'first-feature=value,second-feature',
        ];

        $this->window->setTargetName($expected['targetName']);
        $this->window->setWidth($expected['width']);
        $this->window->setHeight($expected['height']);
        $this->window->setWindowFeatures($expected['windowFeatures']);

        $result = $this->window->toArray();

        $this->assertEquals($expected, $result);
    }
}
