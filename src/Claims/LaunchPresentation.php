<?php

namespace Packback\Lti1p3\Claims;

class LaunchPresentation extends Claim
{
    public static function key(): string
    {
        return Claim::LAUNCH_PRESENTATION;
    }
}
