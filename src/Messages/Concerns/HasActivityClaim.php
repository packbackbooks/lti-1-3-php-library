<?php

namespace Packback\Lti1p3\Messages\Concerns;

use Packback\Lti1p3\Claims\Activity;

trait HasActivityClaim
{
    public function activityClaim(): Activity
    {
        return Activity::create($this);
    }
}
