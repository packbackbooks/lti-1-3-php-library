<?php

namespace Packback\Lti1p3\Messages;

use Packback\Lti1p3\Claims\MessageType;
use Packback\Lti1p3\Claims\ResourceLink;
use Packback\Lti1p3\Claims\TargetLinkUri;
use Packback\Lti1p3\LtiConstants;

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

    public function resourceLinkClaim(): ResourceLink
    {
        return ResourceLink::create($this);
    }
}
