<?php

namespace Packback\Lti1p3;

use DateTime;
use InvalidArgumentException;

class LtiGrade
{
    private $score_given;
    private $score_maximum;
    private $comment;
    private $activity_progress;
    private $grading_progress;
    private $timestamp;
    private $user_id;
    private $submission_review;
    private $canvas_extension;

    public function __construct(array $grade = null)
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
    }

    public function __toString()
    {
        // Additionally, includes the call back to filter out only NULL values
        $request = array_filter([
            'scoreGiven' => $this->score_given,
            'scoreMaximum' => $this->score_maximum,
            'comment' => $this->comment,
            'activityProgress' => $this->activity_progress,
            'gradingProgress' => $this->grading_progress,
            'timestamp' => $this->timestamp,
            'userId' => $this->user_id,
            'submissionReview' => $this->submission_review,
            'https://canvas.instructure.com/lti/submission' => $this->canvas_extension,
        ], '\Packback\Lti1p3\Helpers\Helpers::checkIfNullValue');

        return json_encode($request);
    }

    /**
     * Static function to allow for method chaining without having to assign to a variable first.
     */
    public static function new()
    {
        return new LtiGrade();
    }

    public function getScoreGiven()
    {
        return $this->score_given;
    }

    public function setScoreGiven($value)
    {
        $this->score_given = $value;

        return $this;
    }

    public function getScoreMaximum()
    {
        return $this->score_maximum;
    }

    public function setScoreMaximum($value)
    {
        $this->score_maximum = $value;

        return $this;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    public function getActivityProgress()
    {
        return $this->activity_progress;
    }

    public function setActivityProgress($value)
    {
        $this->activity_progress = $value;

        return $this;
    }

    public function getGradingProgress()
    {
        return $this->grading_progress;
    }

    public function setGradingProgress($value)
    {
        $this->grading_progress = $value;

        return $this;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function setTimestamp($value)
    {
        $this->validateTimestamp($value);

        $this->timestamp = $value;

        return $this;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($value)
    {
        $this->user_id = $value;

        return $this;
    }

    public function getSubmissionReview()
    {
        return $this->submission_review;
    }

    public function setSubmissionReview($value)
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
    public function setCanvasExtension($value)
    {
        $this->canvas_extension = $value;

        return $this;
    }

    /**
     * Validates that the given time is an ISO 8601 string with sub-second precision
     * per the LTI spec. Some LMS (e.g. Schoology) include validation logic that will
     * throw a validation error if this isn't enforced.
     *
     * @see https://www.imsglobal.org/spec/lti-ags/v2p0#timestamp
     * @throws InvalidArgumentException Thrown if the given timestamp is not an ISO8601
     *   compatible string with sub-second precision.
     */
    private function validateTimestamp(string $timestamp): void
    {
        // Attempt to parse the timestamp as an ISO8601 string.
        $dt = DateTime::createFromFormat(DateTime::ATOM, $timestamp);

        // Check if the parsed timestamp contains sub-second precision
        if ($dt && strpos($timestamp, '.') !== false) {
            return;
        }

        throw new InvalidArgumentException("Invalid timestamp: $timestamp. ".
            "Timestamp must be in ISO 8601 format with sub-second precision.");
    }
}
