<?php

namespace Tests;

use Packback\Lti1p3\LtiLineitem;

class LtiLineitemTest extends TestCase
{
    private LtiLineitem $lineItem;
    protected function setUp(): void
    {
        $this->lineItem = new LtiLineitem;
    }

    public function test_it_instantiates()
    {
        $this->assertInstanceOf(LtiLineitem::class, $this->lineItem);
    }

    public function test_it_creates_a_new_instance()
    {
        $grade = LtiLineitem::new();

        $this->assertInstanceOf(LtiLineitem::class, $grade);
    }

    public function test_it_gets_id()
    {
        $expected = 'expected';
        $grade = new LtiLineitem(['id' => $expected]);

        $result = $grade->getId();

        $this->assertEquals($expected, $result);
    }

    public function test_it_sets_id()
    {
        $expected = 'expected';

        $this->lineItem->setId($expected);

        $this->assertEquals($expected, $this->lineItem->getId());
    }

    public function test_it_gets_score_maximum()
    {
        $expected = 'expected';
        $grade = new LtiLineitem(['scoreMaximum' => $expected]);

        $result = $grade->getScoreMaximum();

        $this->assertEquals($expected, $result);
    }

    public function test_it_sets_score_maximum()
    {
        $expected = 'expected';

        $this->lineItem->setScoreMaximum($expected);

        $this->assertEquals($expected, $this->lineItem->getScoreMaximum());
    }

    public function test_it_gets_label()
    {
        $expected = 'expected';
        $grade = new LtiLineitem(['label' => $expected]);

        $result = $grade->getLabel();

        $this->assertEquals($expected, $result);
    }

    public function test_it_sets_label()
    {
        $expected = 'expected';

        $this->lineItem->setLabel($expected);

        $this->assertEquals($expected, $this->lineItem->getLabel());
    }

    public function test_it_gets_resource_id()
    {
        $expected = 'expected';
        $grade = new LtiLineitem(['resourceId' => $expected]);

        $result = $grade->getResourceId();

        $this->assertEquals($expected, $result);
    }

    public function test_it_sets_resource_id()
    {
        $expected = 'expected';

        $this->lineItem->setResourceId($expected);

        $this->assertEquals($expected, $this->lineItem->getResourceId());
    }

    public function test_it_gets_resource_link_id()
    {
        $expected = 'expected';
        $grade = new LtiLineitem(['resourceLinkId' => $expected]);

        $result = $grade->getResourceLinkId();

        $this->assertEquals($expected, $result);
    }

    public function test_it_sets_resource_link_id()
    {
        $expected = 'expected';

        $this->lineItem->setResourceLinkId($expected);

        $this->assertEquals($expected, $this->lineItem->getResourceLinkId());
    }

    public function test_it_gets_tag()
    {
        $expected = 'expected';
        $grade = new LtiLineitem(['tag' => $expected]);

        $result = $grade->getTag();

        $this->assertEquals($expected, $result);
    }

    public function test_it_sets_tag()
    {
        $expected = 'expected';

        $this->lineItem->setTag($expected);

        $this->assertEquals($expected, $this->lineItem->getTag());
    }

    public function test_it_gets_start_date_time()
    {
        $expected = 'expected';
        $grade = new LtiLineitem(['startDateTime' => $expected]);

        $result = $grade->getStartDateTime();

        $this->assertEquals($expected, $result);
    }

    public function test_it_sets_start_date_time()
    {
        $expected = 'expected';

        $this->lineItem->setStartDateTime($expected);

        $this->assertEquals($expected, $this->lineItem->getStartDateTime());
    }

    public function test_it_gets_end_date_time()
    {
        $expected = 'expected';
        $grade = new LtiLineitem(['endDateTime' => $expected]);

        $result = $grade->getEndDateTime();

        $this->assertEquals($expected, $result);
    }

    public function test_it_sets_end_date_time()
    {
        $expected = 'expected';

        $this->lineItem->setEndDateTime($expected);

        $this->assertEquals($expected, $this->lineItem->getEndDateTime());
    }

    public function test_it_gets_grades_released(): void
    {
        $expected = true;
        $grade = new LtiLineitem(['gradesReleased' => $expected]);

        $result = $grade->getGradesReleased();

        $this->assertEquals($expected, $result);
    }

    public function test_it_sets_grades_released(): void
    {
        $expected = false;

        $this->lineItem->setGradesReleased($expected);

        $this->assertEquals($expected, $this->lineItem->getGradesReleased());
    }

    public function test_grades_released_constructed_nullable(): void
    {
        $grade = new LtiLineitem;

        $result = $grade->getGradesReleased();

        $this->assertNull($result);
    }

    public function test_grades_released_set_nullable(): void
    {
        $this->lineItem->setGradesReleased(null);

        $this->assertNull($this->lineItem->getGradesReleased());
    }

    public function test_it_casts_full_object_to_string()
    {
        $expected = [
            'id' => 'Id',
            'scoreMaximum' => 'ScoreMaximum',
            'label' => 'Label',
            'resourceId' => 'ResourceId',
            'resourceLinkId' => 'ResourceLinkId',
            'tag' => 'Tag',
            'startDateTime' => 'StartDateTime',
            'endDateTime' => 'EndDateTime',
            'gradesReleased' => true,
        ];

        $lineItem = new LtiLineitem($expected);

        $this->assertEquals(json_encode($expected), (string) $lineItem);
    }

    public function test_it_casts_empty_object_to_string()
    {
        $this->assertEquals('[]', (string) $this->lineItem);
    }
}
