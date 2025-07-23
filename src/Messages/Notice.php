<?php

namespace Packback\Lti1p3\Messages;

use Packback\Lti1p3\MessageValidators\NoticeMessageValidator;

abstract class Notice extends LtiMessage
{
    public static function messageValidator(): string
    {
        return NoticeMessageValidator::class;
    }

    public function sub()
    {
        return $this->getBody()['sub'] ?? null;
    }
}
