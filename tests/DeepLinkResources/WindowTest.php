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

    protected function setUp(): void
    {
        $this->window = new Window(self::INITIAL_TARGET_NAME,
            self::INITIAL_WIDTH, self::INITIAL_HEIGHT, self::INITIAL_WINDOW_FEATURES);
    }
    private Window $window;

    public function test_it_instantiates()
    {
        $this->assertInstanceOf(Window::class, $this->window);
    }

    public function test_it_creates_a_new_instance()
    {
        $DeepLinkResources = Window::new();

        $this->assertInstanceOf(Window::class, $DeepLinkResources);
    }

    public function test_it_gets_target_name()
    {
        $result = $this->window->getTargetName();

        $this->assertEquals(self::INITIAL_TARGET_NAME, $result);
    }

    public function test_it_sets_target_name()
    {
        $expected = 'expected';

        $result = $this->window->setTargetName($expected);

        $this->assertSame($this->window, $result);
        $this->assertEquals($expected, $this->window->getTargetName());
    }

    public function test_it_gets_width()
    {
        $result = $this->window->getWidth();

        $this->assertEquals(self::INITIAL_WIDTH, $result);
    }

    public function test_it_sets_width()
    {
        $expected = 300;

        $result = $this->window->setWidth($expected);

        $this->assertSame($this->window, $result);
        $this->assertEquals($expected, $this->window->getWidth());
    }

    public function test_it_gets_height()
    {
        $result = $this->window->getHeight();

        $this->assertEquals(self::INITIAL_HEIGHT, $result);
    }

    public function test_it_sets_height()
    {
        $expected = 400;

        $result = $this->window->setHeight($expected);

        $this->assertSame($this->window, $result);
        $this->assertEquals($expected, $this->window->getHeight());
    }

    public function test_it_gets_window_features()
    {
        $result = $this->window->getWindowFeatures();

        $this->assertEquals(self::INITIAL_WINDOW_FEATURES, $result);
    }

    public function test_it_sets_window_features()
    {
        $expected = 'first-feature=value,second-feature';

        $result = $this->window->setWindowFeatures($expected);

        $this->assertSame($this->window, $result);
        $this->assertEquals($expected, $this->window->getWindowFeatures());
    }

    public function test_it_creates_array()
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
