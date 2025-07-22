<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\LtiConstants;

class AssignmentGradeService extends Claim
{
    public static function key(): string
    {
        return LtiConstants::AGS_CLAIM_ENDPOINT;
    }
}
