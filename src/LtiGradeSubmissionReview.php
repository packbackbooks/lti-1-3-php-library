<?php

namespace Packback\Lti1p3;

use Packback\Lti1p3\Concerns\JsonStringable;
use Packback\Lti1p3\Concerns\NewChainable;

/**
 * @TODO Next major version, move this into the AGS namespace
 *
 * @phpstan-consistent-constructor
 */
class LtiGradeSubmissionReview
{
    use JsonStringable;
    use NewChainable;
    private $reviewable_status;
    private $label;
    private $url;
    private $custom;

    public function __construct(?array $gradeSubmission = null)
    {
        $this->reviewable_status = $gradeSubmission['reviewableStatus'] ?? null;
        $this->label = $gradeSubmission['label'] ?? null;
        $this->url = $gradeSubmission['url'] ?? null;
        $this->custom = $gradeSubmission['custom'] ?? null;
    }

    public function getArray(): array
    {
        return [
            'reviewableStatus' => $this->reviewable_status,
            'label' => $this->label,
            'url' => $this->url,
            'custom' => $this->custom,
        ];
    }

    public function getReviewableStatus()
    {
        return $this->reviewable_status;
    }

    public function setReviewableStatus($value): self
    {
        $this->reviewable_status = $value;

        return $this;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setLabel($value): self
    {
        $this->label = $value;

        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getCustom()
    {
        return $this->custom;
    }

    public function setCustom($value): self
    {
        $this->custom = $value;

        return $this;
    }
}
