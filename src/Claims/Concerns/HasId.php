<?php

namespace Packback\Lti1p3\Claims\Concerns;

trait HasId
{
    abstract public function getBody();

    public function id()
    {
        return $this->getBody()['id'];
    }
}
