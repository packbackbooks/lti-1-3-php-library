<?php

namespace Packback\Lti1p3\MessageValidators;

use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\LtiException;

class NoticeMessageValidator
{
    /**
     * @throws LtiException
     */
    public static function validate(array $jwtBody): void
    {
        if (!isset($jwtBody[Claim::VERSION])) {
            throw new LtiException('Missing LTI Version');
        }
        if ($jwtBody[Claim::VERSION] !== LtiConstants::V1_3) {
            throw new LtiException('Incorrect version, expected 1.3.0');
        }
    }
}
