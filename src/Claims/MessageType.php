<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\LtiConstants;

class MessageType extends Claim
{
    public static function key(): string
    {
        return LtiConstants::MESSAGE_TYPE;
    }
}
