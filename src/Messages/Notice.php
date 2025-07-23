<?php

namespace Packback\Lti1p3\Messages;

use Packback\Lti1p3\Claims\Notice as NoticeClaim;
use Packback\Lti1p3\MessageValidators\NoticeMessageValidator;

class Notice extends LtiMessage
{
    public static function requiredClaims(): array
    {
        return [];
    }

    public static function messageValidator(): string
    {
        return NoticeMessageValidator::class;
    }

    public function noticeClaim(): NoticeClaim
    {
        return NoticeClaim::create($this);
    }
}
