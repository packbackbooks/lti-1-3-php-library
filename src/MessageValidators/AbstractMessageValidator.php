<?php

namespace Packback\Lti1p3\MessageValidators;

use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Interfaces\IMessageValidator;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\LtiException;

abstract class AbstractMessageValidator implements IMessageValidator
{
    #[\Deprecated(message: 'Message validation is now handled by the Packback\Lti1p3\Factories\MessageFactory class', since: '6.4')]
    abstract public static function getMessageType(): string;

    #[\Deprecated(message: 'Message validation is now handled by the Packback\Lti1p3\Factories\MessageFactory class', since: '6.4')]
    abstract public static function validate(array $jwtBody): void;

    #[\Deprecated(message: 'Message validation is now handled by the Packback\Lti1p3\Factories\MessageFactory class', since: '6.4')]
    public static function canValidate(array $jwtBody): bool
    {
        return $jwtBody[Claim::MESSAGE_TYPE] === static::getMessageType();
    }

    /**
     * @throws LtiException
     */
    #[\Deprecated(message: 'Message validation is now handled by the Packback\Lti1p3\Factories\MessageFactory class', since: '6.4')]
    public static function validateGenericMessage(array $jwtBody): void
    {
        if (empty($jwtBody['sub'])) {
            throw new LtiException('Must have a user (sub)');
        }
        if (!isset($jwtBody[Claim::VERSION])) {
            throw new LtiException('Missing LTI Version');
        }
        if ($jwtBody[Claim::VERSION] !== LtiConstants::V1_3) {
            throw new LtiException('Incorrect version, expected 1.3.0');
        }
        if (!isset($jwtBody[Claim::ROLES])) {
            throw new LtiException('Missing Roles Claim');
        }
    }
}
