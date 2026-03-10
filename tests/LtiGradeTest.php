<?php

namespace Tests;

use Packback\Lti1p3\LtiGrade;

class LtiGradeTest extends TestCase
{
    private $grade;
    protected function setUp(): void
    {
        $this->grade = new LtiGrade;
    }

    public function test_it_instantiates()
    {
        $this->assertInstanceOf(LtiGrade::class, $this->grade);
    }

    public function test_it_creates_a_new_instance()
    {
        $grade = LtiGrade::new();

        $this->assertInstanceOf(LtiGrade::class, $grade);
    }

    public function test_it_gets_score_given()
    {
        $expected = 'expected';
        $grade = new LtiGrade(['scoreGiven' => $expected]);

        $result = $grade->getScoreGiven();

        $this->assertEquals($expected, $result);
    }

    public function test_it_sets_score_given()
    {
        $expected = 'expected';

        $this->grade->setScoreGiven($expected);

        $this->assertEquals($expected, $this->grade->getScoreGiven());
    }

    public function test_it_gets_score_maximum()
    {
        $expected = 'expected';
        $grade = new LtiGrade(['scoreMaximum' => $expected]);

        $result = $grade->getScoreMaximum();

        $this->assertEquals($expected, $result);
    }

    public function test_it_sets_score_maximum()
    {
        $expected = 'expected';

        $this->grade->setScoreMaximum($expected);

        $this->assertEquals($expected, $this->grade->getScoreMaximum());
    }

    public function test_it_gets_comment()
    {
        $expected = 'expected';
        $grade = new LtiGrade(['comment' => $expected]);

        $result = $grade->getComment();

        $this->assertEquals($expected, $result);
    }

    public function test_it_sets_comment()
    {
        $expected = 'expected';

        $this->grade->setComment($expected);

        $this->assertEquals($expected, $this->grade->getComment());
    }

    public function test_it_gets_activity_progress()
    {
        $expected = 'expected';
        $grade = new LtiGrade(['activityProgress' => $expected]);

        $result = $grade->getActivityProgress();

        $this->assertEquals($expected, $result);
    }

    public function test_it_sets_activity_progress()
    {
        $expected = 'expected';

        $this->grade->setActivityProgress($expected);

        $this->assertEquals($expected, $this->grade->getActivityProgress());
    }

    public function test_it_gets_grading_progress()
    {
        $expected = 'expected';
        $grade = new LtiGrade(['gradingProgress' => $expected]);

        $result = $grade->getGradingProgress();

        $this->assertEquals($expected, $result);
    }

    public function test_it_sets_grading_progress()
    {
        $expected = 'expected';

        $this->grade->setGradingProgress($expected);

        $this->assertEquals($expected, $this->grade->getGradingProgress());
    }

    public function test_it_gets_timestamp()
    {
        $expected = 'expected';
        $grade = new LtiGrade(['timestamp' => $expected]);

        $result = $grade->getTimestamp();

        $this->assertEquals($expected, $result);
    }

    public function test_it_sets_timestamp()
    {
        $expected = 'expected';

        $this->grade->setTimestamp($expected);

        $this->assertEquals($expected, $this->grade->getTimestamp());
    }

    public function test_it_gets_user_id()
    {
        $expected = 'expected';
        $grade = new LtiGrade(['userId' => $expected]);

        $result = $grade->getUserId();

        $this->assertEquals($expected, $result);
    }

    public function test_it_sets_user_id()
    {
        $expected = 'expected';

        $this->grade->setUserId($expected);

        $this->assertEquals($expected, $this->grade->getUserId());
    }

    public function test_it_gets_submission_review()
    {
        $expected = 'expected';
        $grade = new LtiGrade(['submissionReview' => $expected]);

        $result = $grade->getSubmissionReview();

        $this->assertEquals($expected, $result);
    }

    public function test_it_sets_submission_review()
    {
        $expected = 'expected';

        $this->grade->setSubmissionReview($expected);

        $this->assertEquals($expected, $this->grade->getSubmissionReview());
    }

    public function test_it_gets_canvas_extension()
    {
        $expected = 'expected';
        $grade = new LtiGrade(['https://canvas.instructure.com/lti/submission' => $expected]);

        $result = $grade->getCanvasExtension();

        $this->assertEquals($expected, $result);
    }

    public function test_it_sets_canvas_extension()
    {
        $expected = 'expected';

        $this->grade->setCanvasExtension($expected);

        $this->assertEquals($expected, $this->grade->getCanvasExtension());
    }

    public function test_it_casts_full_object_to_string()
    {
        $expected = [
            'scoreGiven' => 5,
            'scoreMaximum' => 10,
            'comment' => 'Comment',
            'activityProgress' => 'ActivityProgress',
            'gradingProgress' => 'GradingProgress',
            'timestamp' => 'Timestamp',
            'userId' => 'UserId',
            'submissionReview' => 'SubmissionReview',
            'https://canvas.instructure.com/lti/submission' => 'CanvasExtension',
            'submission' => [
                'startedAt' => '2023-01-15T12:30:45Z',
                'submittedAt' => '2023-01-15T13:15:22Z',
            ],
        ];

        $grade = new LtiGrade($expected);

        $this->assertEquals(json_encode($expected), (string) $grade);
    }

    public function test_it_casts_full_object_to_string_with0_grade()
    {
        $expected = [
            'scoreGiven' => 0,
            'scoreMaximum' => 10,
            'comment' => 'Comment',
            'activityProgress' => 'ActivityProgress',
            'gradingProgress' => 'GradingProgress',
            'timestamp' => 'Timestamp',
            'userId' => 'UserId',
            'submissionReview' => 'SubmissionReview',
            'https://canvas.instructure.com/lti/submission' => 'CanvasExtension',
            'submission' => [
                'startedAt' => '2023-01-15T12:30:45Z',
                'submittedAt' => '2023-01-15T13:15:22Z',
            ],
        ];

        $grade = new LtiGrade($expected);

        $this->assertEquals(json_encode($expected), (string) $grade);
    }

    public function test_it_casts_empty_object_to_string()
    {
        $this->assertEquals('[]', (string) $this->grade);
    }

    public function test_it_gets_submission()
    {
        $expected = [
            'startedAt' => '2023-01-15T12:30:45Z',
            'submittedAt' => '2023-01-15T13:15:22Z',
        ];
        $grade = new LtiGrade(['submission' => $expected]);

        $result = $grade->getSubmission();

        $this->assertEquals($expected, $result);
    }

    public function test_it_sets_submission()
    {
        $expected = [
            'startedAt' => '2023-01-15T12:30:45Z',
            'submittedAt' => '2023-01-15T13:15:22Z',
        ];

        $this->grade->setSubmission($expected);

        $this->assertEquals($expected, $this->grade->getSubmission());
    }

}
