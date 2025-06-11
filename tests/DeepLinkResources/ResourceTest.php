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

    protected function setUp(): void
    {
        $this->resource = new Resource;
    }

    public function test_it_instantiates()
    {
        $this->assertInstanceOf(Resource::class, $this->resource);
    }

    public function test_it_creates_a_new_instance()
    {
        $resource = Resource::new();

        $this->assertInstanceOf(Resource::class, $resource);
    }

    public function test_it_gets_type()
    {
        $result = $this->resource->getType();

        $this->assertEquals('ltiResourceLink', $result);
    }

    public function test_it_sets_type()
    {
        $expected = 'expected';

        $result = $this->resource->setType($expected);

        $this->assertSame($this->resource, $result);
        $this->assertEquals($expected, $this->resource->getType());
    }

    public function test_it_gets_title()
    {
        $result = $this->resource->getTitle();

        $this->assertNull($result);
    }

    public function test_it_sets_title()
    {
        $expected = 'expected';

        $result = $this->resource->setTitle($expected);

        $this->assertSame($this->resource, $result);
        $this->assertEquals($expected, $this->resource->getTitle());
    }

    public function test_it_gets_text()
    {
        $result = $this->resource->getText();

        $this->assertNull($result);
    }

    public function test_it_sets_text()
    {
        $expected = 'expected';

        $result = $this->resource->setText($expected);

        $this->assertSame($this->resource, $result);
        $this->assertEquals($expected, $this->resource->getText());
    }

    public function test_it_gets_url()
    {
        $result = $this->resource->getUrl();

        $this->assertNull($result);
    }

    public function test_it_sets_url()
    {
        $expected = 'expected';

        $result = $this->resource->setUrl($expected);

        $this->assertSame($this->resource, $result);
        $this->assertEquals($expected, $this->resource->getUrl());
    }

    public function test_it_gets_lineitem()
    {
        $result = $this->resource->getLineItem();

        $this->assertNull($result);
    }

    public function test_it_sets_lineitem()
    {
        $expected = Mockery::mock(LtiLineitem::class);

        $result = $this->resource->setLineItem($expected);

        $this->assertSame($this->resource, $result);
        $this->assertEquals($expected, $this->resource->getLineItem());
    }

    public function test_it_gets_icon()
    {
        $result = $this->resource->getIcon();

        $this->assertNull($result);
    }

    public function test_it_sets_icon()
    {
        $expected = Mockery::mock(Icon::class);

        $result = $this->resource->setIcon($expected);

        $this->assertSame($this->resource, $result);
        $this->assertEquals($expected, $this->resource->getIcon());
    }

    public function test_it_gets_thumbnail()
    {
        $result = $this->resource->getThumbnail();

        $this->assertNull($result);
    }

    public function test_it_sets_thumbnail()
    {
        $expected = Mockery::mock(Icon::class);

        $result = $this->resource->setThumbnail($expected);

        $this->assertSame($this->resource, $result);
        $this->assertEquals($expected, $this->resource->getThumbnail());
    }

    public function test_it_gets_custom_params()
    {
        $result = $this->resource->getCustomParams();

        $this->assertEquals([], $result);
    }

    public function test_it_sets_custom_params()
    {
        $expected = ['a_key' => 'a_value'];

        $result = $this->resource->setCustomParams($expected);

        $this->assertSame($this->resource, $result);
        $this->assertEquals($expected, $this->resource->getCustomParams());
    }

    public function test_it_gets_iframe()
    {
        $result = $this->resource->getIframe();

        $this->assertNull($result);
    }

    public function test_it_sets_iframe()
    {
        $expected = new Iframe;

        $result = $this->resource->setIframe($expected);

        $this->assertSame($this->resource, $result);
        $this->assertEquals($expected, $this->resource->getIframe());
    }

    public function test_it_gets_window()
    {
        $result = $this->resource->getWindow();

        $this->assertNull($result);
    }

    public function test_it_sets_window()
    {
        $expected = new Window;

        $result = $this->resource->setWindow($expected);

        $this->assertSame($this->resource, $result);
        $this->assertEquals($expected, $this->resource->getWindow());
    }

    public function test_it_gets_availability_interval()
    {
        $result = $this->resource->getAvailabilityInterval();

        $this->assertNull($result);
    }

    public function test_it_sets_availability_interval()
    {
        $expected = new DateTimeInterval;

        $result = $this->resource->setAvailabilityInterval($expected);

        $this->assertSame($this->resource, $result);
        $this->assertEquals($expected, $this->resource->getAvailabilityInterval());
    }

    public function test_it_gets_submission_interval()
    {
        $result = $this->resource->getSubmissionInterval();

        $this->assertNull($result);
    }

    public function test_it_sets_submission_interval()
    {
        $expected = new DateTimeInterval;

        $result = $this->resource->setSubmissionInterval($expected);

        $this->assertSame($this->resource, $result);
        $this->assertEquals($expected, $this->resource->getSubmissionInterval());
    }

    public function test_it_creates_array_with_null_date_time_intervals()
    {
        $expected = [
            'type' => LtiConstants::DL_RESOURCE_LINK_TYPE,
        ];

        $this->resource->setAvailabilityInterval(null);
        $this->resource->setSubmissionInterval(null);

        $result = $this->resource->toArray();

        $this->assertEquals($expected, $result);
    }

    public function test_it_creates_array_with_date_time_intervals_having_null_dates()
    {
        $availabilityInterval = new DateTimeInterval(date_create(), null);
        $submissionInterval = new DateTimeInterval(null, date_create());

        $expected = [
            'type' => LtiConstants::DL_RESOURCE_LINK_TYPE,
            'available' => [
                'startDateTime' => $availabilityInterval->getStart()->format(\DateTime::ATOM),
            ],
            'submission' => [
                'endDateTime' => $submissionInterval->getEnd()->format(\DateTime::ATOM),
            ],
        ];

        $this->resource->setAvailabilityInterval($availabilityInterval);
        $this->resource->setSubmissionInterval($submissionInterval);

        $result = $this->resource->toArray();

        $this->assertEquals($expected, $result);
    }

    public function test_it_creates_array_with_defined_optional_properties()
    {
        $icon = Icon::new('https://example.com/image.png', 100, 200);
        $Iframe = new Iframe;
        $window = new Window;
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
                'resourceId' => 'lineitem_resourceId',
                'tag' => 'lineitem_tag',
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
        $lineitem->shouldReceive('getResourceId')
            ->twice()->andReturn($expected['lineItem']['resourceId']);
        $lineitem->shouldReceive('getTag')
            ->twice()->andReturn($expected['lineItem']['tag']);

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
