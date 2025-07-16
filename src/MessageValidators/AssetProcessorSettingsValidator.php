<?php

namespace Packback\Lti1p3\MessageValidators;

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

        if (empty($jwtBody[LtiConstants::AP_CLAIM_ACTIVITY])) {
            throw new LtiException('Missing Activity Claim');
        }
        if (empty($jwtBody[LtiConstants::AP_CLAIM_CONTEXT])) {
            throw new LtiException('Missing Context Claim');
        }
    }
}
