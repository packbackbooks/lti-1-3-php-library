<?php

namespace Packback\Lti1p3\Messages\Concerns;

use Packback\Lti1p3\Claims\Context;

trait HasContextClaim
{
    public function contextClaim(): Context
    {
        return Context::create($this);
    }
}
