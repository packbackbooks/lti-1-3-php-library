<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\Claims\Concerns\HasScope;

class AssignmentGradeService extends Claim
{
    use HasScope;

    public static function claimKey(): string
    {
        return Claim::AGS_ENDPOINT;
    }

    public function lineitems(): string
    {
        return $this->getBody()['lineitems'];
    }
}
