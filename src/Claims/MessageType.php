<?php

namespace Packback\Lti1p3\Claims;

class MessageType extends Claim
{
    public static function key(): string
    {
        return Claim::MESSAGE_TYPE;
    }
}
