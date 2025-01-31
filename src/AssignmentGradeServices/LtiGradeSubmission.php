<?php

namespace Packback\Lti1p3\AssignmentGradeServices;

use Packback\Lti1p3\Concerns\JsonStringable;
use Packback\Lti1p3\Concerns\NewChainable;

/**
 * @internal
 *
 * @phpstan-consistent-constructor
 **/
class LtiGradeSubmission
{
    use JsonStringable;
    use NewChainable;
    private $started_at;
    private $submitted_at;

    public function __construct(?array $grade = null)
    {
        $this->started_at = $grade['startedAt'] ?? null;
        $this->submitted_at = $grade['submittedAt'] ?? null;
    }

    public function getArray(): array
    {
        return [
            'startedAt' => $this->started_at,
            'submittedAt' => $this->submitted_at,
        ];
    }

    public function getStartedAt()
    {
        return $this->started_at;
    }

    public function setStartedAt($value): self
    {
        $this->started_at = $value;

        return $this;
    }

    public function getSumbmittedAt()
    {
        return $this->submitted_at;
    }

    public function setSumbmittedAt($value): self
    {
        $this->submitted_at = $value;

        return $this;
    }
}
