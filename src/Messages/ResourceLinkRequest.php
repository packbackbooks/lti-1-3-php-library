<?php

namespace Packback\Lti1p3\Messages;

use Packback\Lti1p3\Claims\Context;
use Packback\Lti1p3\Claims\Custom;
use Packback\Lti1p3\Claims\LaunchPresentation;
use Packback\Lti1p3\Claims\Lis;
use Packback\Lti1p3\Claims\MessageType;
use Packback\Lti1p3\Claims\ResourceLink;
use Packback\Lti1p3\Claims\RoleScopeMentor;
use Packback\Lti1p3\Claims\TargetLinkUri;
use Packback\Lti1p3\Claims\ToolPlatform;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\MessageValidators\ResourceMessageValidator;

class ResourceLinkRequest extends LaunchMessage
{
    public static function messageType(): string
    {
        return LtiConstants::MESSAGE_TYPE_RESOURCE;
    }

    public static function requiredClaims(): array
    {
        return [
            MessageType::claimKey(),
            TargetLinkUri::claimKey(),
            ResourceLink::claimKey(),
        ];
    }

    public static function optionalClaims(): array
    {
        return [
            Context::claimKey(),
            ToolPlatform::claimKey(),
            RoleScopeMentor::claimKey(),
            LaunchPresentation::claimKey(),
            Lis::claimKey(),
            Custom::claimKey(),
        ];
    }

    public static function messageValidator(): string
    {
        return ResourceMessageValidator::class;
    }

    public function resourceLinkClaim(): ResourceLink
    {
        return ResourceLink::create($this);
    }
}
