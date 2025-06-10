<?php

namespace Tests\DeepLinkResources;

use DateTime;
use Packback\Lti1p3\DeepLinkResources\DateTimeInterval;
use Packback\Lti1p3\LtiException;
use Tests\TestCase;

class DateTimeIntervalTest extends TestCase
{
    private DateTime $initialStart;
    private DateTime $initialEnd;
    private DateTimeInterval $dateTimeInterval;

    protected function setUp(): void
    {
        $this->initialStart = date_create();
        $this->initialEnd = date_create();
        $this->dateTimeInterval = new DateTimeInterval($this->initialStart, $this->initialEnd);
    }

    public function test_it_instantiates()
    {
        $this->assertInstanceOf(DateTimeInterval::class, $this->dateTimeInterval);
    }

    public function test_it_creates_a_new_instance()
    {
        $DeepLinkResources = DateTimeInterval::new();

        $this->assertInstanceOf(DateTimeInterval::class, $DeepLinkResources);
    }

    public function test_it_instantiates_with_null_start_end_values()
    {
        $dateTimeInterval = new DateTimeInterval(null, null);

        $this->assertInstanceOf(DateTimeInterval::class, $dateTimeInterval);
        $this->assertNull($dateTimeInterval->getStart());
        $this->assertNull($dateTimeInterval->getEnd());
    }

    public function test_it_instantiates_with_null_start()
    {
        $end = date_create('+1 day');
        $dateTimeInterval = new DateTimeInterval(null, $end);

        $this->assertInstanceOf(DateTimeInterval::class, $dateTimeInterval);
        $this->assertNull($dateTimeInterval->getStart());
        $this->assertEquals($end, $dateTimeInterval->getEnd());
    }

    public function test_it_instantiates_with_null_end()
    {
        $start = date_create();
        $dateTimeInterval = new DateTimeInterval($start, null);

        $this->assertInstanceOf(DateTimeInterval::class, $dateTimeInterval);
        $this->assertEquals($start, $dateTimeInterval->getStart());
        $this->assertNull($dateTimeInterval->getEnd());
    }

    public function test_it_gets_start()
    {
        $result = $this->dateTimeInterval->getStart();

        $this->assertEquals($this->initialStart, $result);
    }

    public function test_it_sets_start()
    {
        $expected = date_create('+1 day');

        $result = $this->dateTimeInterval->setStart($expected);

        $this->assertSame($this->dateTimeInterval, $result);
        $this->assertEquals($expected, $this->dateTimeInterval->getStart());
    }

    public function test_it_gets_end()
    {
        $result = $this->dateTimeInterval->getEnd();

        $this->assertEquals($this->initialEnd, $result);
    }

    public function test_it_sets_end()
    {
        $expected = date_create('+1 day');

        $result = $this->dateTimeInterval->setEnd($expected);

        $this->assertSame($this->dateTimeInterval, $result);
        $this->assertEquals($expected, $this->dateTimeInterval->getEnd());
    }

    public function test_it_sets_start_to_null()
    {
        $result = $this->dateTimeInterval->setStart(null);

        $this->assertSame($this->dateTimeInterval, $result);
        $this->assertNull($this->dateTimeInterval->getStart());
    }

    public function test_it_sets_end_to_null()
    {
        $result = $this->dateTimeInterval->setEnd(null);

        $this->assertSame($this->dateTimeInterval, $result);
        $this->assertNull($this->dateTimeInterval->getEnd());
    }

    public function test_it_creates_array_with_null_start()
    {
        $expectedEnd = date_create('+1 day');
        $expected = [
            'endDateTime' => $expectedEnd->format(DateTime::ATOM),
        ];

        $this->dateTimeInterval->setStart(null);
        $this->dateTimeInterval->setEnd($expectedEnd);

        $result = $this->dateTimeInterval->toArray();

        $this->assertEquals($expected, $result);
    }

    public function test_it_creates_array_with_null_end()
    {
        $expectedStart = date_create('+1 day');
        $expected = [
            'startDateTime' => $expectedStart->format(DateTime::ATOM),
        ];

        $this->dateTimeInterval->setStart($expectedStart);
        $this->dateTimeInterval->setEnd(null);

        $result = $this->dateTimeInterval->toArray();

        $this->assertEquals($expected, $result);
    }

    public function test_it_creates_array_with_both_null()
    {
        $expected = [];

        $this->dateTimeInterval->setStart(null);
        $this->dateTimeInterval->setEnd(null);

        $result = $this->dateTimeInterval->toArray();

        $this->assertEquals($expected, $result);
    }

    public function test_it_throws_exception_when_creating_array_with_invalid_time_interval()
    {
        $this->dateTimeInterval->setStart(date_create());
        $this->dateTimeInterval->setEnd(date_create('-1 day'));

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(DateTimeInterval::ERROR_START_GT_END);

        $this->dateTimeInterval->toArray();
    }

    public function test_it_creates_array_with_defined_optional_properties()
    {
        $expectedStart = date_create('+1 day');
        $expectedEnd = date_create('+2 days');
        $expected = [
            'startDateTime' => $expectedStart->format(DateTime::ATOM),
            'endDateTime' => $expectedEnd->format(DateTime::ATOM),
        ];

        $this->dateTimeInterval->setStart($expectedStart);
        $this->dateTimeInterval->setEnd($expectedEnd);

        $result = $this->dateTimeInterval->toArray();

        $this->assertEquals($expected, $result);
    }
}
