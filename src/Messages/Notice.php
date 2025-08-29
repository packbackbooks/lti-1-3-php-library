<?php

namespace Packback\Lti1p3\Messages;

abstract class Notice extends LtiMessage
{
    public function sub()
    {
        return $this->getBody()['sub'] ?? null;
    }
}
