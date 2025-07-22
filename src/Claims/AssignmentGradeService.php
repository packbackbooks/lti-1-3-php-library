<?php

namespace Packback\Lti1p3\Claims;

class AssignmentGradeService extends Claim
{
    public static function key(): string
    {
        return Claim::AGS_ENDPOINT;
    }
}
