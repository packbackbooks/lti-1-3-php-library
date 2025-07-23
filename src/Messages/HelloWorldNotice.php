<?php

namespace Packback\Lti1p3\Messages;

class HelloWorldNotice extends Notice
{
    public static function requiredClaims(): array
    {
        return [];
    }
}
