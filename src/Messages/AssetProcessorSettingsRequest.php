<?php

namespace Packback\Lti1p3\Messages;

use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\MessageValidators\AssetProcessorSettingsValidator;

class AssetProcessorSettingsRequest extends LaunchMessage
{
    public static function messageType(): string
    {
        return LtiConstants::MESSAGE_TYPE_ASSETPROCESSORSETTINGS;
    }

    public static function requiredClaims(): array
    {
        return [
            LtiConstants::MESSAGE_TYPE,
            LtiConstants::TARGET_LINK_URI,
            LtiConstants::AP_CLAIM_ACTIVITY,
            LtiConstants::CONTEXT,
        ];
    }

    public static function optionalClaims(): array
    {
        return [
            LtiConstants::ROLE_SCOPE_MENTOR,
            LtiConstants::TOOL_PLATFORM,
            LtiConstants::LAUNCH_PRESENTATION,
            LtiConstants::CUSTOM,
            LtiConstants::LIS,
        ];
    }

    public static function messageValidator(): string
    {
        return AssetProcessorSettingsValidator::class;
    }
}
