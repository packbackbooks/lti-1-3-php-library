<?php

namespace Packback\Lti1p3\Claims;

class ForUser extends Claim
{
    public static function claimKey(): string
    {
        return Claim::FOR_USER;
    }
    public function userId()
    {
        return $this->getBody()['user_id'];
    }
}
