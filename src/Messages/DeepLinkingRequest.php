<?php

namespace Packback\Lti1p3\Messages;

use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\LtiDeepLink;
use Packback\Lti1p3\MessageValidators\DeepLinkMessageValidator;

class DeepLinkingRequest extends LaunchMessage
{
    public static function messageType(): string
    {
        return LtiConstants::MESSAGE_TYPE_DEEPLINK;
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

    public static function messageValidator(): string
    {
        return DeepLinkMessageValidator::class;
    }

    /**
     * Fetches a deep link that can be used to construct a deep linking response.
     */
    public function getDeepLink(): LtiDeepLink
    {
        return new LtiDeepLink(
            $this->registration,
            $this->getClaim(LtiConstants::DEPLOYMENT_ID),
            $this->getClaim(LtiConstants::DL_DEEP_LINK_SETTINGS)
        );
    }
}
