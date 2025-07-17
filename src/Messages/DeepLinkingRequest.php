<?php

namespace Packback\Lti1p3\Messages;

use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\MessageValidators\DeepLinkMessageValidator;

class DeepLinkingRequest extends LaunchMessage
{
    public static function messageType(): string
    {
        return LtiContstants::MESSAGE_TYPE_DEEPLINK;
    }

    public static function requiredClaims(): array
    {
        return [
            LtiConstants::MESSAGE_TYPE,
            LtiConstants::DL_DEEP_LINK_SETTINGS,
        ];
    }

    public static function optionalClaims(): array
    {
        return [
            LtiConstants::LAUNCH_PRESENTATION,
            LtiConstants::TOOL_PLATFORM,
            LtiConstants::CONTEXT,
            LtiConstants::ROLE_SCOPE_MENTOR,
            LtiConstants::CUSTOM,
        ];
    }

    protected function messageValidator(): string
    {
        return DeepLinkMessageValidator::class;
    }
}
