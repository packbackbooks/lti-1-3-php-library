<?php

namespace Tests\Payloads;

use Packback\Lti1p3\Payloads\AssetProcessor;
use Tests\TestCase;

class AssetProcessorTest extends TestCase
{
    protected function setUp(): void
    {
        $this->assetProcessor = new AssetProcessor;
    }
    private AssetProcessor $assetProcessor;

    public function test_it_creates_a_new_instance()
    {
        $assetProcessor = AssetProcessor::new();
        $this->assertInstanceOf(AssetProcessor::class, $assetProcessor);
    }

    public function test_it_gets_title()
    {
        $result = $this->assetProcessor->getTitle();
        $this->assertNull($result);
    }

    public function test_it_sets_title()
    {
        $expected = 'Test Asset Processor Title';
        $result = $this->assetProcessor->setTitle($expected);

        $this->assertSame($this->assetProcessor, $result);
        $this->assertEquals($expected, $this->assetProcessor->getTitle());
    }

    public function test_it_gets_text()
    {
        $result = $this->assetProcessor->getText();
        $this->assertNull($result);
    }

    public function test_it_sets_text()
    {
        $expected = 'Test Asset Processor Description';
        $result = $this->assetProcessor->setText($expected);

        $this->assertSame($this->assetProcessor, $result);
        $this->assertEquals($expected, $this->assetProcessor->getText());
    }

    public function test_it_gets_url()
    {
        $result = $this->assetProcessor->getUrl();
        $this->assertNull($result);
    }

    public function test_it_sets_url()
    {
        $expected = 'https://example.com/asset-processor';
        $result = $this->assetProcessor->setUrl($expected);

        $this->assertSame($this->assetProcessor, $result);
        $this->assertEquals($expected, $this->assetProcessor->getUrl());
    }

    public function test_it_gets_report()
    {
        $result = $this->assetProcessor->getReport();
        $this->assertNull($result);
    }

    public function test_it_sets_report()
    {
        $expected = [
            'status' => 'success',
            'message' => 'Asset processed successfully',
        ];
        $result = $this->assetProcessor->setReport($expected);

        $this->assertSame($this->assetProcessor, $result);
        $this->assertEquals($expected, $this->assetProcessor->getReport());
    }

    public function test_it_gets_custom()
    {
        $result = $this->assetProcessor->getCustom();
        $this->assertNull($result);
    }

    public function test_it_sets_custom()
    {
        $expected = [
            'custom_param1' => 'value1',
            'custom_param2' => 'value2',
        ];
        $result = $this->assetProcessor->setCustom($expected);

        $this->assertSame($this->assetProcessor, $result);
        $this->assertEquals($expected, $this->assetProcessor->getCustom());
    }

    public function test_it_creates_array_with_no_optional_properties()
    {
        $expected = [
            'type' => 'ltiAssetProcessor',
        ];

        $result = $this->assetProcessor->toArray();
        $this->assertEquals($expected, $result);
    }

    public function test_it_creates_array_with_defined_optional_properties()
    {
        $expectedTitle = 'Test Title';
        $expectedText = 'Test Description';
        $expectedUrl = 'https://example.com/processor';
        $expectedReport = ['status' => 'complete'];
        $expectedCustom = ['param' => 'value'];

        $this->assetProcessor->setTitle($expectedTitle);
        $this->assetProcessor->setText($expectedText);
        $this->assetProcessor->setUrl($expectedUrl);
        $this->assetProcessor->setReport($expectedReport);
        $this->assetProcessor->setCustom($expectedCustom);

        $expected = [
            'type' => 'ltiAssetProcessor',
            'title' => $expectedTitle,
            'text' => $expectedText,
            'url' => $expectedUrl,
            'report' => $expectedReport,
            'custom' => $expectedCustom,
        ];

        $result = $this->assetProcessor->toArray();
        $this->assertEquals($expected, $result);
    }

    public function test_it_creates_array_with_mixed_properties()
    {
        $expectedTitle = 'Mixed Properties Test';
        $expectedCustom = ['test' => 'data'];

        $this->assetProcessor->setTitle($expectedTitle);
        $this->assetProcessor->setCustom($expectedCustom);

        $expected = [
            'type' => 'ltiAssetProcessor',
            'title' => $expectedTitle,
            'custom' => $expectedCustom,
        ];

        $result = $this->assetProcessor->toArray();
        $this->assertEquals($expected, $result);
    }

    public function test_fluent_interface_chaining()
    {
        $result = $this->assetProcessor
            ->setTitle('Chained Title')
            ->setText('Chained Text')
            ->setUrl('https://example.com/chained')
            ->setReport(['chained' => 'report'])
            ->setCustom(['chained' => 'custom']);

        $this->assertSame($this->assetProcessor, $result);
        $this->assertEquals('Chained Title', $this->assetProcessor->getTitle());
        $this->assertEquals('Chained Text', $this->assetProcessor->getText());
        $this->assertEquals('https://example.com/chained', $this->assetProcessor->getUrl());
        $this->assertEquals(['chained' => 'report'], $this->assetProcessor->getReport());
        $this->assertEquals(['chained' => 'custom'], $this->assetProcessor->getCustom());
    }
}
