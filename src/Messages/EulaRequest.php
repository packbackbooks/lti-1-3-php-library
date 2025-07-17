<?php

namespace Packback\Lti1p3;

use Packback\Lti1p3\MessageValidators\AssetProcessorSettingsValidator;

class EulaRequest extends LtiMessage
{
    public static function requiredClaims(): array
    {
        return [
            LtiConstants::MESSAGE_TYPE,
            LtiConstants::VERSION,
            LtiConstants::DEPLOYMENT_ID,
            LtiConstants::TARGET_LINK_URI,
            LtiConstants::RESOURCE_LINK,
            LtiConstants::ROLES,
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

    public static function messageType(): string
    {
        return LtiConstants::MESSAGE_TYPE_EULA;
    }

    protected function getMessageValidator(array $jwtBody): ?string
    {
        return AssetProcessorSettingsValidator::class;
    }
}
