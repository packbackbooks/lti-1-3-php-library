<?php

namespace Packback\Lti1p3\Claims;

/**
 * MessageType Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti/claim/message_type
 *
 * Example payload:
 * {
 *     "https://purl.imsglobal.org/spec/lti/claim/message_type": "LtiResourceLinkRequest"
 * }
 */
class MessageType extends Claim
{
    public static function claimKey(): string
    {
        return Claim::MESSAGE_TYPE;
    }
}
