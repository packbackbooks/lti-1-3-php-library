<?php

namespace Packback\Lti1p3;

use Packback\Lti1p3\Concerns\JsonStringable;

class LtiGrade
{
    use JsonStringable;
    public const ACTIVITY_PROGRESS_INITIALIZED = 'Initialized';
    public const ACTIVITY_PROGRESS_STARTED = 'Started';
    public const ACTIVITY_PROGRESS_IN_PROGRESS = 'InProgress';
    public const ACTIVITY_PROGRESS_SUBMITTED = 'Submitted';
    public const ACTIVITY_PROGRESS_COMPLETED = 'Completed';
    public const GRADING_PROGRESS_NOT_READY = 'NotReady';
    public const GRADING_PROGRESS_FAILED = 'Failed';
    public const GRADING_PROGRESS_PENDING = 'Pending';
    public const GRADING_PROGRESS_PENDING_MANUAL = 'PendingManual';
    public const GRADING_PROGRESS_FULLY_GRADED = 'FullyGraded';
    private $score_given;
    private $score_maximum;
    private $comment;
    private $activity_progress;
    private $grading_progress;
    private $timestamp;
    private $user_id;
    private $submission_review;
    private $submission;
    private $canvas_extension;

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
        $this->submission = $grade['submission'] ?? null;
        $this->canvas_extension = $grade['https://canvas.instructure.com/lti/submission'] ?? null;
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

    public function getActivityProgress(): string
    {
        return $this->activity_progress;
    }

    public function setActivityProgress($value): self
    {
        self::validateProgress($value, 'activity');

        $this->activity_progress = $value;

        return $this;
    }

    public function getGradingProgress(): string
    {
        return $this->grading_progress;
    }

    public function setGradingProgress($value): self
    {
        self::validateProgress($value, 'grading');

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

    public function getSubmission()
    {
        return $this->submission;
    }

    public function setSubmission($value): self
    {
        $this->submission = $value;

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

    private static function validateProgress(string $value, string $name): void
    {
        $progresses = [
            'activity' => [
                self::ACTIVITY_PROGRESS_INITIALIZED,
                self::ACTIVITY_PROGRESS_STARTED,
                self::ACTIVITY_PROGRESS_IN_PROGRESS,
                self::ACTIVITY_PROGRESS_SUBMITTED,
                self::ACTIVITY_PROGRESS_COMPLETED,
            ],
            'grading' => [
                self::GRADING_PROGRESS_NOT_READY,
                self::GRADING_PROGRESS_FAILED,
                self::GRADING_PROGRESS_PENDING,
                self::GRADING_PROGRESS_PENDING_MANUAL,
                self::GRADING_PROGRESS_FULLY_GRADED,
            ],
        ];
        $progressGroup = $progresses[$name];

        if (in_array($value, $progressGroup)) {
            $message = `Invalid $name progress: $value. Must be one of: `.implode(', ', $progressGroup);
            throw new \UnexpectedValueException($message);
        }
    }
}
