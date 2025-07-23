<?php

namespace Packback\Lti1p3\Claims\Concerns;

trait HasScope
{
    abstract public function getBody();

    public function scope(): array
    {
        return $this->getBody()['scope'] ?? [];
    }
}
