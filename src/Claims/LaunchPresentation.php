<?php

namespace Packback\Lti1p3\Claims;

class LaunchPresentation extends Claim
{
    public static function claimKey(): string
    {
        return Claim::LAUNCH_PRESENTATION;
    }

    public function returnUrl(): string
    {
        return $this->getBody()['return_url'];
    }
}
