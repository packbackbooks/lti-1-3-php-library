<?php

namespace BNSoftware\Lti1p3;

class LtiGradeSubmissionReview
{
    private $reviewableStatus;
    private $label;
    private $url;
    private $custom;

    public function __construct(array $gradeSubmission = null)
    {
        $this->reviewableStatus = $gradeSubmission['reviewableStatus'] ?? null;
        $this->label = $gradeSubmission['label'] ?? null;
        $this->url = $gradeSubmission['url'] ?? null;
        $this->custom = $gradeSubmission['custom'] ?? null;
    }

    public function __toString()
    {
        // Additionally, includes the call back to filter out only NULL values
        return json_encode(
            array_filter(
                [
                    'reviewableStatus' => $this->reviewableStatus,
                    'label'            => $this->label,
                    'url'              => $this->url,
                    'custom'           => $this->custom,
                ],
                '\BNSoftware\Lti1p3\Helpers\Helpers::checkIfNullValue'
            )
        );
    }

    /**
     * Static function to allow for method chaining without having to assign to a variable first.
     */
    public static function new()
    {
        return new LtiGradeSubmissionReview();
    }

    public function getReviewableStatus()
    {
        return $this->reviewableStatus;
    }

    public function setReviewableStatus($value)
    {
        $this->reviewableStatus = $value;

        return $this;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setLabel($value)
    {
        $this->label = $value;

        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    public function getCustom()
    {
        return $this->custom;
    }

    public function setCustom($value)
    {
        $this->custom = $value;

        return $this;
    }
}
