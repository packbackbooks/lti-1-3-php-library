<?php

namespace Packback\Lti1p3\Claims;

class EulaService extends Claim
{
    public static function key(): string
    {
        return Claim::EULASERVICE;
    }
}
