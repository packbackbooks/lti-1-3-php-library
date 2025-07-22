<?php

namespace Packback\Lti1p3\Claims;

class Submission extends Claim
{
    public static function key(): string
    {
        return Claim::SUBMISSION;
    }
}
