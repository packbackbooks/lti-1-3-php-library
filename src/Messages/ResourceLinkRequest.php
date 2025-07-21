<?php

namespace Packback\Lti1p3\Messages;

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
            LtiConstants::TARGET_LINK_URI,
            LtiConstants::RESOURCE_LINK,
        ];
    }

    public static function optionalClaims(): array
    {
        return [
            LtiConstants::CONTEXT,
            LtiConstants::TOOL_PLATFORM,
            LtiConstants::ROLE_SCOPE_MENTOR,
            LtiConstants::LAUNCH_PRESENTATION,
            LtiConstants::LIS,
            LtiConstants::CUSTOM,
        ];
    }

    public static function messageValidator(): string
    {
        return ResourceMessageValidator::class;
    }
}
