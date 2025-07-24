<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\Claims\Concerns\HasId;

class Context extends Claim
{
    use HasId;

    public static function claimKey(): string
    {
        return Claim::CONTEXT;
    }

    public function label()
    {
        return $this->getBody()['label'];
    }

    public function title()
    {
        return $this->getBody()['title'];
    }

    public function type()
    {
        return $this->getBody()['type'];
    }
}
