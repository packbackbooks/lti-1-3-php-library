<?php

namespace Packback\Lti1p3\Messages;

use Packback\Lti1p3\Claims\Claim;
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
            LtiConstants::MESSAGE_TYPE,
            Claim::TARGET_LINK_URI,
            Claim::RESOURCE_LINK,
        ];
    }

    public static function optionalClaims(): array
    {
        return [
            Claim::CONTEXT,
            Claim::TOOL_PLATFORM,
            Claim::ROLE_SCOPE_MENTOR,
            Claim::LAUNCH_PRESENTATION,
            Claim::LIS,
            Claim::CUSTOM,
        ];
    }

    public static function messageValidator(): string
    {
        return ResourceMessageValidator::class;
    }
}
