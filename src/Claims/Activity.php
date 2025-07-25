<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\Claims\Concerns\HasId;

/**
 * Activity Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti/claim/activity
 *
 * Example payload:
 * {
 *     "https://purl.imsglobal.org/spec/lti/claim/activity": {
 *         "id": "4b4482b3-c445-4729-8e2d-6cbcda248efc"
 *     }
 * }
 */
class Activity extends Claim
{
    use HasId;

    public static function claimKey(): string
    {
        return Claim::ACTIVITY;
    }
}
