<?php

namespace Packback\Lti1p3\MessageValidators;

use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\LtiException;

class EulaMessageValidator extends AbstractMessageValidator
{
    public static function getMessageType(): string
    {
        return LtiConstants::MESSAGE_TYPE_EULA;
    }

    /**
     * @throws LtiException
     */
    public static function validate(array $jwtBody): void
    {
        static::validateGenericMessage($jwtBody);

        if (empty($jwtBody[Claim::EULASERVICE])) {
            throw new LtiException('Missing EULA Service Claim');
        }
    }
}
