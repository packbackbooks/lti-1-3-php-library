<?php

namespace Tests;

use Packback\Lti1p3\LtiGradeSubmissionReview;

class LtiGradeSubmissionReviewTest extends TestCase
{
    protected function setUp(): void
    {
        $this->gradeReview = new LtiGradeSubmissionReview;
    }
    private $gradeReview;

    public function test_it_instantiates()
    {
        $this->assertInstanceOf(LtiGradeSubmissionReview::class, $this->gradeReview);
    }

    public function test_creates_a_new_instance()
    {
        $review = LtiGradeSubmissionReview::new();

        $this->assertInstanceOf(LtiGradeSubmissionReview::class, $review);
    }

    public function test_it_gets_reviewable_status()
    {
        $expected = 'ReviewableStatus';
        $gradeReview = new LtiGradeSubmissionReview(['reviewableStatus' => 'ReviewableStatus']);

        $result = $gradeReview->getReviewableStatus();

        $this->assertEquals($expected, $result);
    }

    public function test_it_sets_reviewable_status()
    {
        $expected = 'expected';

        $this->gradeReview->setReviewableStatus($expected);

        $this->assertEquals($expected, $this->gradeReview->getReviewableStatus());
    }

    public function test_it_gets_label()
    {
        $expected = 'Label';
        $gradeReview = new LtiGradeSubmissionReview(['label' => 'Label']);

        $result = $gradeReview->getLabel();

        $this->assertEquals($expected, $result);
    }

    public function test_it_sets_label()
    {
        $expected = 'expected';

        $this->gradeReview->setLabel($expected);

        $this->assertEquals($expected, $this->gradeReview->getLabel());
    }

    public function test_it_gets_url()
    {
        $expected = 'Url';
        $gradeReview = new LtiGradeSubmissionReview(['url' => 'Url']);

        $result = $gradeReview->getUrl();

        $this->assertEquals($expected, $result);
    }

    public function test_it_sets_url()
    {
        $expected = 'expected';

        $this->gradeReview->setUrl($expected);

        $this->assertEquals($expected, $this->gradeReview->getUrl());
    }

    public function test_it_gets_custom()
    {
        $expected = 'Custom';
        $gradeReview = new LtiGradeSubmissionReview(['custom' => 'Custom']);

        $result = $gradeReview->getCustom();

        $this->assertEquals($expected, $result);
    }

    public function test_it_sets_custom()
    {
        $expected = 'expected';

        $this->gradeReview->setCustom($expected);

        $this->assertEquals($expected, $this->gradeReview->getCustom());
    }

    public function test_it_casts_full_object_to_string()
    {
        $expected = [
            'reviewableStatus' => 'ReviewableStatus',
            'label' => 'Label',
            'url' => 'Url',
            'custom' => 'Custom',
        ];

        $gradeReview = new LtiGradeSubmissionReview($expected);

        $this->assertEquals(json_encode($expected), (string) $gradeReview);
    }

    public function test_it_casts_empty_object_to_string()
    {
        $this->assertEquals('[]', (string) $this->gradeReview);
    }
}
