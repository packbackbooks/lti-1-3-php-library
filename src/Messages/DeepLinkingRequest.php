<?php

namespace Packback\Lti1p3\Messages;

use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Factories\ClaimFactory;
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
            Claim::DL_DEEP_LINK_SETTINGS,
        ];
    }

    public static function optionalClaims(): array
    {
        return [
            Claim::LAUNCH_PRESENTATION,
            Claim::TOOL_PLATFORM,
            Claim::CONTEXT,
            Claim::ROLE_SCOPE_MENTOR,
            Claim::CUSTOM,
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
            ClaimFactory::createDeploymentId($this)->getBody(),
            ClaimFactory::createDeepLinkSettings($this)->getBody()
        );
    }
}
