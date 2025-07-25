<?php

namespace Packback\Lti1p3\Claims\Concerns;

trait HasErrors
{
    abstract public function getBody();

    public function validationContext()
    {
        return $this->getBody()['validation_context'] ?? null;
    }

    public function errors(): array
    {
        return $this->getBody()['errors'] ?? [];
    }
}
