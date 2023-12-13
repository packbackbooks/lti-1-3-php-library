<?php

namespace Packback\Lti1p3;

use DateTime;

class LtiDeepLinkDateTimeInterval
{
    public function __construct(
        private ?DateTime $start = null,
        private ?DateTime $end = null
    ) {
        if (isset($start) && isset($end) && $end < $start) {
            throw new LtiException('Interval start time cannot be greater than end time');
        }
    }

    public static function new(): LtiDeepLinkDateTimeInterval
    {
        return new LtiDeepLinkDateTimeInterval();
    }

    public function setStart(?DateTime $start): LtiDeepLinkDateTimeInterval
    {
        $this->start = $start;

        return $this;
    }

    public function getStart(): ?DateTime
    {
        return $this->start;
    }

    public function setEnd(?DateTime $end): LtiDeepLinkDateTimeInterval
    {
        $this->end = $end;

        return $this;
    }

    public function getEnd(): ?DateTime
    {
        return $this->end;
    }

    public function toArray(): array
    {
        if (!isset($this->start) && !isset($this->end)) {
            throw new LtiException('At least one of the interval bounds must be specified on the object instance');
        }

        if ($this->start !== null && $this->end !== null && $this->end < $this->start) {
            throw new LtiException('Interval start time cannot be greater than end time');
        }

        $dateTimeInterval = [];

        if (isset($this->start)) {
            $dateTimeInterval['startDateTime'] = $this->start->format(DateTime::ATOM);
        }
        if (isset($this->end)) {
            $dateTimeInterval['endDateTime'] = $this->end->format(DateTime::ATOM);
        }

        return $dateTimeInterval;
    }
}
