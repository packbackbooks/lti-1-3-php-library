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

    public function setUp(): void
    {
        $this->initialStart = date_create();
        $this->initialEnd = date_create();
        $this->dateTimeInterval = new DateTimeInterval($this->initialStart, $this->initialEnd);
    }

    public function testItInstantiates()
    {
        $this->assertInstanceOf(DateTimeInterval::class, $this->dateTimeInterval);
    }

    public function testItCreatesANewInstance()
    {
        $DeepLinkResources = DateTimeInterval::new();

        $this->assertInstanceOf(DateTimeInterval::class, $DeepLinkResources);
    }

    public function testItGetsStart()
    {
        $result = $this->dateTimeInterval->getStart();

        $this->assertEquals($this->initialStart, $result);
    }

    public function testItSetsStart()
    {
        $expected = date_create('+1 day');

        $result = $this->dateTimeInterval->setStart($expected);

        $this->assertSame($this->dateTimeInterval, $result);
        $this->assertEquals($expected, $this->dateTimeInterval->getStart());
    }

    public function testItGetsEnd()
    {
        $result = $this->dateTimeInterval->getEnd();

        $this->assertEquals($this->initialEnd, $result);
    }

    public function testItSetsEnd()
    {
        $expected = date_create('+1 day');

        $result = $this->dateTimeInterval->setEnd($expected);

        $this->assertSame($this->dateTimeInterval, $result);
        $this->assertEquals($expected, $this->dateTimeInterval->getEnd());
    }

    public function testItThrowsExceptionWhenCreatingArrayWithBothPropertiesNull()
    {
        $this->dateTimeInterval->setStart(null);
        $this->dateTimeInterval->setEnd(null);

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(DateTimeInterval::ERROR_NO_START_OR_END);

        $this->dateTimeInterval->toArray();
    }

    public function testItThrowsExceptionWhenCreatingArrayWithInvalidTimeInterval()
    {
        $this->dateTimeInterval->setStart(date_create());
        $this->dateTimeInterval->setEnd(date_create('-1 day'));

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage(DateTimeInterval::ERROR_START_GT_END);

        $this->dateTimeInterval->toArray();
    }

    public function testItCreatesArrayWithDefinedOptionalProperties()
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
