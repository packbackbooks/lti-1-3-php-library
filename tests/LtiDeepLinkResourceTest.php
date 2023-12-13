<?php

namespace Tests;

use Mockery;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\LtiDeepLinkDateTimeInterval;
use Packback\Lti1p3\LtiDeepLinkResource;
use Packback\Lti1p3\LtiDeepLinkResourceIcon;
use Packback\Lti1p3\LtiDeepLinkResourceIframe;
use Packback\Lti1p3\LtiDeepLinkResourceWindow;
use Packback\Lti1p3\LtiLineitem;

class LtiDeepLinkResourceTest extends TestCase
{
    private LtiDeepLinkResource $deepLinkResource;

    public function setUp(): void
    {
        $this->deepLinkResource = new LtiDeepLinkResource();
    }

    public function testItInstantiates()
    {
        $this->assertInstanceOf(LtiDeepLinkResource::class, $this->deepLinkResource);
    }

    public function testItCreatesANewInstance()
    {
        $deepLinkResource = LtiDeepLinkResource::new();

        $this->assertInstanceOf(LtiDeepLinkResource::class, $deepLinkResource);
    }

    public function testItGetsType()
    {
        $result = $this->deepLinkResource->getType();

        $this->assertEquals('ltiResourceLink', $result);
    }

    public function testItSetsType()
    {
        $expected = 'expected';

        $result = $this->deepLinkResource->setType($expected);

        $this->assertSame($this->deepLinkResource, $result);
        $this->assertEquals($expected, $this->deepLinkResource->getType());
    }

    public function testItGetsTitle()
    {
        $result = $this->deepLinkResource->getTitle();

        $this->assertNull($result);
    }

    public function testItSetsTitle()
    {
        $expected = 'expected';

        $result = $this->deepLinkResource->setTitle($expected);

        $this->assertSame($this->deepLinkResource, $result);
        $this->assertEquals($expected, $this->deepLinkResource->getTitle());
    }

    public function testItGetsText()
    {
        $result = $this->deepLinkResource->getText();

        $this->assertNull($result);
    }

    public function testItSetsText()
    {
        $expected = 'expected';

        $result = $this->deepLinkResource->setText($expected);

        $this->assertSame($this->deepLinkResource, $result);
        $this->assertEquals($expected, $this->deepLinkResource->getText());
    }

    public function testItGetsUrl()
    {
        $result = $this->deepLinkResource->getUrl();

        $this->assertNull($result);
    }

    public function testItSetsUrl()
    {
        $expected = 'expected';

        $result = $this->deepLinkResource->setUrl($expected);

        $this->assertSame($this->deepLinkResource, $result);
        $this->assertEquals($expected, $this->deepLinkResource->getUrl());
    }

    public function testItGetsLineitem()
    {
        $result = $this->deepLinkResource->getLineItem();

        $this->assertNull($result);
    }

    public function testItSetsLineitem()
    {
        $expected = Mockery::mock(LtiLineitem::class);

        $result = $this->deepLinkResource->setLineItem($expected);

        $this->assertSame($this->deepLinkResource, $result);
        $this->assertEquals($expected, $this->deepLinkResource->getLineItem());
    }

    public function testItGetsIcon()
    {
        $result = $this->deepLinkResource->getIcon();

        $this->assertNull($result);
    }

    public function testItSetsIcon()
    {
        $expected = Mockery::mock(LtiDeepLinkResourceIcon::class);

        $result = $this->deepLinkResource->setIcon($expected);

        $this->assertSame($this->deepLinkResource, $result);
        $this->assertEquals($expected, $this->deepLinkResource->getIcon());
    }

    public function testItGetsThumbnail()
    {
        $result = $this->deepLinkResource->getThumbnail();

        $this->assertNull($result);
    }

    public function testItSetsThumbnail()
    {
        $expected = Mockery::mock(LtiDeepLinkResourceIcon::class);

        $result = $this->deepLinkResource->setThumbnail($expected);

        $this->assertSame($this->deepLinkResource, $result);
        $this->assertEquals($expected, $this->deepLinkResource->getThumbnail());
    }

    public function testItGetsCustomParams()
    {
        $result = $this->deepLinkResource->getCustomParams();

        $this->assertEquals([], $result);
    }

    public function testItSetsCustomParams()
    {
        $expected = ['a_key' => 'a_value'];

        $result = $this->deepLinkResource->setCustomParams($expected);

        $this->assertSame($this->deepLinkResource, $result);
        $this->assertEquals($expected, $this->deepLinkResource->getCustomParams());
    }

    public function testItGetsIframe()
    {
        $result = $this->deepLinkResource->getIframe();

        $this->assertEquals(null, $result);
    }

    public function testItSetsIframe()
    {
        $expected = new LtiDeepLinkResourceIframe();

        $result = $this->deepLinkResource->setIframe($expected);

        $this->assertSame($this->deepLinkResource, $result);
        $this->assertEquals($expected, $this->deepLinkResource->getIframe());
    }

    public function testItCreatesArrayWithoutOptionalProperties()
    {
        $expected = [
            'type' => LtiConstants::DL_RESOURCE_LINK_TYPE,
            'presentation' => [
                'documentTarget' => 'iframe',
            ],
        ];

        $result = $this->deepLinkResource->toArray();

        $this->assertEquals($expected, $result);
    }

    public function testItCreatesArrayWithDefinedOptionalProperties()
    {
        $icon = LtiDeepLinkResourceIcon::new('https://example.com/image.png', 100, 200);
        $resourceIframe = new LtiDeepLinkResourceIframe();
        $resourceWindow = new LtiDeepLinkResourceWindow();
        $resourceDateTimeInterval = new LtiDeepLinkDateTimeInterval(date_create());

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
            'iframe' => $resourceIframe->toArray(),
            'window' => $resourceWindow->toArray(),
            'available' => $resourceDateTimeInterval->toArray(),
            'submission' => $resourceDateTimeInterval->toArray(),
        ];

        $lineitem = Mockery::mock(LtiLineitem::class);
        $lineitem->shouldReceive('getScoreMaximum')
            ->twice()->andReturn($expected['lineItem']['scoreMaximum']);
        $lineitem->shouldReceive('getLabel')
            ->twice()->andReturn($expected['lineItem']['label']);

        $this->deepLinkResource->setTitle($expected['title']);
        $this->deepLinkResource->setText($expected['text']);
        $this->deepLinkResource->setUrl($expected['url']);
        $this->deepLinkResource->setIcon($icon);
        $this->deepLinkResource->setThumbnail($icon);
        $this->deepLinkResource->setLineItem($lineitem);
        $this->deepLinkResource->setIframe($resourceIframe);
        $this->deepLinkResource->setWindow($resourceWindow);
        $this->deepLinkResource->setAvailabilityInterval($resourceDateTimeInterval);
        $this->deepLinkResource->setSubmissionInterval($resourceDateTimeInterval);

        $result = $this->deepLinkResource->toArray();

        $this->assertEquals($expected, $result);

        // Test again with custom params
        $expected['custom'] = ['a_key' => 'a_value'];
        $this->deepLinkResource->setCustomParams(['a_key' => 'a_value']);
        $result = $this->deepLinkResource->toArray();
        $this->assertEquals($expected, $result);
    }
}
