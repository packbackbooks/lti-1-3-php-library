<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\Claims\Concerns\HasId;

class Notice extends Claim
{
    use HasId;
    public static function claimKey(): string
    {
        return Claim::NOTICE;
    }

    public function sub()
    {
        return $this->getBody()['sub'] ?? null;
    }

    public function type(): string
    {
        return $this->getBody()['type'];
    }

    public function timestamp(): string
    {
        return $this->getBody()['timestamp'];
    }
}
