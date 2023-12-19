<?php

namespace Tests\DeepLinkResources;

use Mockery;
use Packback\Lti1p3\DeepLinkResources\DateTimeInterval;
use Packback\Lti1p3\DeepLinkResources\Icon;
use Packback\Lti1p3\DeepLinkResources\Iframe;
use Packback\Lti1p3\DeepLinkResources\Resource;
use Packback\Lti1p3\DeepLinkResources\Window;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\LtiLineitem;
use Tests\TestCase;

class ResourceTest extends TestCase
{
    private Resource $resource;

    public function setUp(): void
    {
        $this->resource = new Resource();
    }

    public function testItInstantiates()
    {
        $this->assertInstanceOf(Resource::class, $this->resource);
    }

    public function testItCreatesANewInstance()
    {
        $resource = Resource::new();

        $this->assertInstanceOf(Resource::class, $resource);
    }

    public function testItGetsType()
    {
        $result = $this->resource->getType();

        $this->assertEquals('ltiResourceLink', $result);
    }

    public function testItSetsType()
    {
        $expected = 'expected';

        $result = $this->resource->setType($expected);

        $this->assertSame($this->resource, $result);
        $this->assertEquals($expected, $this->resource->getType());
    }

    public function testItGetsTitle()
    {
        $result = $this->resource->getTitle();

        $this->assertNull($result);
    }

    public function testItSetsTitle()
    {
        $expected = 'expected';

        $result = $this->resource->setTitle($expected);

        $this->assertSame($this->resource, $result);
        $this->assertEquals($expected, $this->resource->getTitle());
    }

    public function testItGetsText()
    {
        $result = $this->resource->getText();

        $this->assertNull($result);
    }

    public function testItSetsText()
    {
        $expected = 'expected';

        $result = $this->resource->setText($expected);

        $this->assertSame($this->resource, $result);
        $this->assertEquals($expected, $this->resource->getText());
    }

    public function testItGetsUrl()
    {
        $result = $this->resource->getUrl();

        $this->assertNull($result);
    }

    public function testItSetsUrl()
    {
        $expected = 'expected';

        $result = $this->resource->setUrl($expected);

        $this->assertSame($this->resource, $result);
        $this->assertEquals($expected, $this->resource->getUrl());
    }

    public function testItGetsLineitem()
    {
        $result = $this->resource->getLineItem();

        $this->assertNull($result);
    }

    public function testItSetsLineitem()
    {
        $expected = Mockery::mock(LtiLineitem::class);

        $result = $this->resource->setLineItem($expected);

        $this->assertSame($this->resource, $result);
        $this->assertEquals($expected, $this->resource->getLineItem());
    }

    public function testItGetsIcon()
    {
        $result = $this->resource->getIcon();

        $this->assertNull($result);
    }

    public function testItSetsIcon()
    {
        $expected = Mockery::mock(Icon::class);

        $result = $this->resource->setIcon($expected);

        $this->assertSame($this->resource, $result);
        $this->assertEquals($expected, $this->resource->getIcon());
    }

    public function testItGetsThumbnail()
    {
        $result = $this->resource->getThumbnail();

        $this->assertNull($result);
    }

    public function testItSetsThumbnail()
    {
        $expected = Mockery::mock(Icon::class);

        $result = $this->resource->setThumbnail($expected);

        $this->assertSame($this->resource, $result);
        $this->assertEquals($expected, $this->resource->getThumbnail());
    }

    public function testItGetsCustomParams()
    {
        $result = $this->resource->getCustomParams();

        $this->assertEquals([], $result);
    }

    public function testItSetsCustomParams()
    {
        $expected = ['a_key' => 'a_value'];

        $result = $this->resource->setCustomParams($expected);

        $this->assertSame($this->resource, $result);
        $this->assertEquals($expected, $this->resource->getCustomParams());
    }

    public function testItGetsIframe()
    {
        $result = $this->resource->getIframe();

        $this->assertEquals(null, $result);
    }

    public function testItSetsIframe()
    {
        $expected = new Iframe();

        $result = $this->resource->setIframe($expected);

        $this->assertSame($this->resource, $result);
        $this->assertEquals($expected, $this->resource->getIframe());
    }

    public function testItCreatesArrayWithoutOptionalProperties()
    {
        $expected = [
            'type' => LtiConstants::DL_RESOURCE_LINK_TYPE,
            'presentation' => [
                'documentTarget' => 'iframe',
            ],
        ];

        $result = $this->resource->toArray();

        $this->assertEquals($expected, $result);
    }

    public function testItCreatesArrayWithDefinedOptionalProperties()
    {
        $icon = Icon::new('https://example.com/image.png', 100, 200);
        $Iframe = new Iframe();
        $window = new Window();
        $dateTimeInterval = new DateTimeInterval(date_create());

        $expected = [
            'type' => LtiConstants::DL_RESOURCE_LINK_TYPE,
            'title' => 'a_title',
            'text' => 'a_text',
            'url' => 'a_url',
            'icon' => [
                'url' => $icon->getUrl(),
                'width' => $icon->getWidth(),
                'height' => $icon->getHeight(),
            ],
            'thumbnail' => [
                'url' => $icon->getUrl(),
                'width' => $icon->getWidth(),
                'height' => $icon->getHeight(),
            ],
            'lineItem' => [
                'scoreMaximum' => 80,
                'label' => 'lineitem_label',
            ],
            'iframe' => $Iframe->toArray(),
            'window' => $window->toArray(),
            'available' => $dateTimeInterval->toArray(),
            'submission' => $dateTimeInterval->toArray(),
        ];

        $lineitem = Mockery::mock(LtiLineitem::class);
        $lineitem->shouldReceive('getScoreMaximum')
            ->twice()->andReturn($expected['lineItem']['scoreMaximum']);
        $lineitem->shouldReceive('getLabel')
            ->twice()->andReturn($expected['lineItem']['label']);

        $this->resource->setTitle($expected['title']);
        $this->resource->setText($expected['text']);
        $this->resource->setUrl($expected['url']);
        $this->resource->setIcon($icon);
        $this->resource->setThumbnail($icon);
        $this->resource->setLineItem($lineitem);
        $this->resource->setIframe($Iframe);
        $this->resource->setWindow($window);
        $this->resource->setAvailabilityInterval($dateTimeInterval);
        $this->resource->setSubmissionInterval($dateTimeInterval);

        $result = $this->resource->toArray();

        $this->assertEquals($expected, $result);

        // Test again with custom params
        $expected['custom'] = ['a_key' => 'a_value'];
        $this->resource->setCustomParams(['a_key' => 'a_value']);
        $result = $this->resource->toArray();
        $this->assertEquals($expected, $result);
    }
}
