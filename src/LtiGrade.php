<?php

namespace Packback\Lti1p3;

use Packback\Lti1p3\Concerns\JsonStringable;

class LtiGrade
{
    use JsonStringable;
    private $score_given;
    private $score_maximum;
    private $comment;
    private $activity_progress;
    private $grading_progress;
    private $timestamp;
    private $user_id;
    private $submission_review;
    private $canvas_extension;
    private $submission;

    public function __construct(?array $grade = null)
    {
        $this->score_given = $grade['scoreGiven'] ?? null;
        $this->score_maximum = $grade['scoreMaximum'] ?? null;
        $this->comment = $grade['comment'] ?? null;
        $this->activity_progress = $grade['activityProgress'] ?? null;
        $this->grading_progress = $grade['gradingProgress'] ?? null;
        $this->timestamp = $grade['timestamp'] ?? null;
        $this->user_id = $grade['userId'] ?? null;
        $this->submission_review = $grade['submissionReview'] ?? null;
        $this->canvas_extension = $grade['https://canvas.instructure.com/lti/submission'] ?? null;
        $this->submission = $grade['submission'] ?? null;
    }

    public function getArray(): array
    {
        return [
            'scoreGiven' => $this->score_given,
            'scoreMaximum' => $this->score_maximum,
            'comment' => $this->comment,
            'activityProgress' => $this->activity_progress,
            'gradingProgress' => $this->grading_progress,
            'timestamp' => $this->timestamp,
            'userId' => $this->user_id,
            'submissionReview' => $this->submission_review,
            'https://canvas.instructure.com/lti/submission' => $this->canvas_extension,
            'submission' => $this->submission,
        ];
    }

    /**
     * Static function to allow for method chaining without having to assign to a variable first.
     */
    public static function new(): self
    {
        return new LtiGrade;
    }

    public function getScoreGiven()
    {
        return $this->score_given;
    }

    public function setScoreGiven($value): self
    {
        $this->score_given = $value;

        return $this;
    }

    public function getScoreMaximum()
    {
        return $this->score_maximum;
    }

    public function setScoreMaximum($value): self
    {
        $this->score_maximum = $value;

        return $this;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment($comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getActivityProgress()
    {
        return $this->activity_progress;
    }

    public function setActivityProgress($value): self
    {
        $this->activity_progress = $value;

        return $this;
    }

    public function getGradingProgress()
    {
        return $this->grading_progress;
    }

    public function setGradingProgress($value): self
    {
        $this->grading_progress = $value;

        return $this;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function setTimestamp($value): self
    {
        $this->timestamp = $value;

        return $this;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($value): self
    {
        $this->user_id = $value;

        return $this;
    }

    public function getSubmissionReview()
    {
        return $this->submission_review;
    }

    public function setSubmissionReview($value): self
    {
        $this->submission_review = $value;

        return $this;
    }

    public function getCanvasExtension()
    {
        return $this->canvas_extension;
    }

    /**
     * Add custom extensions for Canvas.
     *
     * Disclaimer: You should only set this if your LMS is Canvas.
     *             Some LMS (e.g. Schoology) include validation logic that will throw if there
     *             is unexpected data. And, the type of LMS cannot simply be inferred by their URL.
     *
     * @see https://documentation.instructure.com/doc/api/score.html
     */
    public function setCanvasExtension($value): self
    {
        $this->canvas_extension = $value;

        return $this;
    }

    /**
     * Get the submission data according to the standard IMS AGS v2.0 specification.
     *
     * @return array|null The submission data or null if not set
     */
    public function getSubmission()
    {
        return $this->submission;
    }

    /**
     * Set the submission data according to the standard IMS AGS v2.0 specification.
     * This can include startedAt and submittedAt timestamps.
     *
     * Example usage:
     * $grade->setSubmission([
     *     'startedAt' => '2023-01-15T12:30:45Z',
     *     'submittedAt' => '2023-01-15T13:15:22Z'
     * ]);
     *
     * @param  array  $value  The submission data containing startedAt and/or submittedAt timestamps
     *
     * @see https://www.imsglobal.org/spec/lti-ags/v2p0
     */
    public function setSubmission($value): self
    {
        $this->submission = $value;

        return $this;
    }
}
