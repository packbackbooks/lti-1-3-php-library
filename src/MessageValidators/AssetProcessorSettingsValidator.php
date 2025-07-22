<?php

namespace Packback\Lti1p3\MessageValidators;

use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\LtiException;

class AssetProcessorSettingsValidator extends AbstractMessageValidator
{
    public static function getMessageType(): string
    {
        return LtiConstants::MESSAGE_TYPE_ASSETPROCESSORSETTINGS;
    }

    /**
     * @throws LtiException
     */
    public static function validate(array $jwtBody): void
    {
        static::validateGenericMessage($jwtBody);

        if (empty($jwtBody[Claim::ACTIVITY])) {
            throw new LtiException('Missing Activity Claim');
        }
        if (empty($jwtBody[Claim::CONTEXT])) {
            throw new LtiException('Missing Context Claim');
        }
    }
}
