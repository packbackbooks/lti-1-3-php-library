<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\Claims\Concerns\HasScope;

class EulaService extends Claim
{
    use HasScope;

    public static function claimKey(): string
    {
        return Claim::EULASERVICE;
    }

    public function url()
    {
        return $this->getBody()['url'] ?? null;
    }
}
