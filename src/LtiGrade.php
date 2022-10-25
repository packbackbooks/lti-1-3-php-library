<?php

namespace BNSoftware\Lti1p3;

class LtiGrade
{
    private $scoreGiven;
    private $scoreMaximum;
    private $comment;
    private $activityProgress;
    private $gradingProgress;
    private $timestamp;
    private $userId;
    private $submissionReview;

    public function __construct(array $grade = null)
    {
        $this->scoreGiven = $grade['scoreGiven'] ?? null;
        $this->scoreMaximum = $grade['scoreMaximum'] ?? null;
        $this->comment = $grade['comment'] ?? null;
        $this->activityProgress = $grade['activityProgress'] ?? null;
        $this->gradingProgress = $grade['gradingProgress'] ?? null;
        $this->timestamp = $grade['timestamp'] ?? null;
        $this->userId = $grade['userId'] ?? null;
        $this->submissionReview = $grade['submissionReview'] ?? null;
        $this->submissionExtension = $grade['https://canvas.instructure.com/lti/submission'] ?? null;
    }

    public function __toString()
    {
        // Additionally, includes the call back to filter out only NULL values
        $request = array_filter(
            [
                'scoreGiven'                                    => $this->scoreGiven,
                'scoreMaximum'                                  => $this->scoreMaximum,
                'comment'                                       => $this->comment,
                'activityProgress'                              => $this->activityProgress,
                'gradingProgress'                               => $this->gradingProgress,
                'timestamp'                                     => $this->timestamp,
                'userId'                                        => $this->userId,
                'submissionReview'                              => $this->submissionReview,
                'https://canvas.instructure.com/lti/submission' => $this->submissionExtension,
            ],
            '\BNSoftware\Lti1p3\Helpers\Helpers::checkIfNullValue'
        );

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
        return $this->scoreGiven;
    }

    public function setScoreGiven($value)
    {
        $this->scoreGiven = $value;

        return $this;
    }

    public function getScoreMaximum()
    {
        return $this->scoreMaximum;
    }

    public function setScoreMaximum($value)
    {
        $this->scoreMaximum = $value;

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
        return $this->activityProgress;
    }

    public function setActivityProgress($value)
    {
        $this->activityProgress = $value;

        return $this;
    }

    public function getGradingProgress()
    {
        return $this->gradingProgress;
    }

    public function setGradingProgress($value)
    {
        $this->gradingProgress = $value;

        return $this;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function setTimestamp($value)
    {
        $this->timestamp = $value;

        return $this;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($value)
    {
        $this->userId = $value;

        return $this;
    }

    public function getSubmissionReview()
    {
        return $this->submissionReview;
    }

    public function setSubmissionReview($value)
    {
        $this->submissionReview = $value;

        return $this;
    }

    public function getCanvasExtension()
    {
        return $this->submissionExtension;
    }

    // Custom Extension for Canvas.
    // https://documentation.instructure.com/doc/api/score.html
    public function setCanvasExtension($value)
    {
        $this->submissionExtension = $value;

        return $this;
    }
}
