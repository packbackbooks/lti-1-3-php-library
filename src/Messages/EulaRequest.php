<?php

namespace Packback\Lti1p3\Messages;

use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\MessageValidators\AssetProcessorSettingsValidator;

class EulaRequest extends LaunchMessage
{
    public static function messageType(): string
    {
        return LtiConstants::MESSAGE_TYPE_EULA;
    }

    public static function requiredClaims(): array
    {
        return [
            LtiConstants::MESSAGE_TYPE,
            LtiConstants::TARGET_LINK_URI,
            LtiConstants::EULA_CLAIM_SERVICE,
        ];
    }

    public static function optionalClaims(): array
    {
        return [
            LtiConstants::CONTEXT,
            LtiConstants::TOOL_PLATFORM,
            LtiConstants::LAUNCH_PRESENTATION,
            LtiConstants::CUSTOM,
            LtiConstants::LIS,
        ];
    }

    protected function messageValidator(): string
    {
        return AssetProcessorSettingsValidator::class;
    }
}
