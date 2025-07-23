<?php

namespace Packback\Lti1p3\Messages;

use Packback\Lti1p3\Claims\Context;
use Packback\Lti1p3\Claims\Custom;
use Packback\Lti1p3\Claims\DeepLinkSettings;
use Packback\Lti1p3\Claims\LaunchPresentation;
use Packback\Lti1p3\Claims\MessageType;
use Packback\Lti1p3\Claims\Roles;
use Packback\Lti1p3\Claims\RoleScopeMentor;
use Packback\Lti1p3\Claims\TargetLinkUri;
use Packback\Lti1p3\Claims\ToolPlatform;
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
            MessageType::claimKey(),
            TargetLinkUri::claimKey(),
            DeepLinkSettings::claimKey(),
        ];
    }

    public static function optionalClaims(): array
    {
        return [
            LaunchPresentation::claimKey(),
            ToolPlatform::claimKey(),
            Context::claimKey(),
            Roles::claimKey(),
            RoleScopeMentor::claimKey(),
            Custom::claimKey(),
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
            $this->claimDeploymentId()->getBody(),
            $this->claimDeepLinkSettings()->getBody()
        );
    }

    public function claimDeepLinkSettings(): DeepLinkSettings
    {
        return DeepLinkSettings::create($this);
    }
}
