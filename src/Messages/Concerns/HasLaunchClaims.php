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
    public function claimMessageType(): MessageType
    {
        return MessageType::create($this);
    }

    public function claimTargetLinkUri(): TargetLinkUri
    {
        return TargetLinkUri::create($this);
    }

    public function claimContext(): Context
    {
        return Context::create($this);
    }

    public function claimToolPlatform(): ToolPlatform
    {
        return ToolPlatform::create($this);
    }

    public function claimRoleScopeMentor(): RoleScopeMentor
    {
        return RoleScopeMentor::create($this);
    }

    public function claimLaunchPresentation(): LaunchPresentation
    {
        return LaunchPresentation::create($this);
    }

    public function claimLis(): Lis
    {
        return Lis::create($this);
    }

    public function claimCustom(): Custom
    {
        return Custom::create($this);
    }

    public function claimLti1p1(): Lti1p1
    {
        return Lti1p1::create($this);
    }
}
