<?php

namespace Packback\Lti1p3\Claims;

class GroupService extends Claim
{
    public static function claimKey(): string
    {
        return Claim::GS_GROUPSSERVICE;
    }
}
