<?php

namespace Packback\Lti1p3\Claims;

class MessageType extends Claim
{
    public static function claimKey(): string
    {
        return Claim::MESSAGE_TYPE;
    }
}
