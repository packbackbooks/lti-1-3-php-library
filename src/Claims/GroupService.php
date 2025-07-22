<?php

namespace Packback\Lti1p3\Claims;

class GroupService extends Claim
{
    public static function key(): string
    {
        return Claim::GS_GROUPSSERVICE;
    }
}
