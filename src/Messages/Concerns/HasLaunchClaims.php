<?php

namespace Packback\Lti1p3\Messages\Concerns;

use Packback\Lti1p3\Claims\Context;
use Packback\Lti1p3\Claims\Custom;
use Packback\Lti1p3\Claims\LaunchPresentation;
use Packback\Lti1p3\Claims\Lis;
use Packback\Lti1p3\Claims\Lti1p1;
use Packback\Lti1p3\Claims\MessageType;
use Packback\Lti1p3\Claims\RoleScopeMentor;
use Packback\Lti1p3\Claims\TargetLinkUri;
use Packback\Lti1p3\Claims\ToolPlatform;

trait HasLaunchClaims
{
    public function messageTypeClaim(): MessageType
    {
        return MessageType::create($this);
    }

    public function targetLinkUriClaim(): TargetLinkUri
    {
        return TargetLinkUri::create($this);
    }

    public function contextClaim(): Context
    {
        return Context::create($this);
    }

    public function toolPlatformClaim(): ToolPlatform
    {
        return ToolPlatform::create($this);
    }

    public function roleScopeMentorClaim(): RoleScopeMentor
    {
        return RoleScopeMentor::create($this);
    }

    public function launchPresentationClaim(): LaunchPresentation
    {
        return LaunchPresentation::create($this);
    }

    public function lisClaim(): Lis
    {
        return Lis::create($this);
    }

    public function customClaim(): Custom
    {
        return Custom::create($this);
    }

    public function ltiClaim1p1(): Lti1p1
    {
        return Lti1p1::create($this);
    }
}
