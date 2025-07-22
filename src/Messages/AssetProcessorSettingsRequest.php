<?php

namespace Packback\Lti1p3\Messages;

use Packback\Lti1p3\Claims\Claim;
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
            Claim::TARGET_LINK_URI,
            Claim::ACTIVITY,
            Claim::CONTEXT,
        ];
    }

    public static function optionalClaims(): array
    {
        return [
            Claim::ROLE_SCOPE_MENTOR,
            Claim::TOOL_PLATFORM,
            Claim::LAUNCH_PRESENTATION,
            Claim::CUSTOM,
            Claim::LIS,
        ];
    }

    public static function messageValidator(): string
    {
        return AssetProcessorSettingsValidator::class;
    }
}
