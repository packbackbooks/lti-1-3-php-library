<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\Claims\Concerns\HasId;

/**
 * Submission Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti/claim/submission
 *
 * Example payload:
 * {
 *     "https://purl.imsglobal.org/spec/lti/claim/submission": {
 *         "id": "0da34e94-33a3-4b14-bf03-9a738ce930a9"
 *     }
 * }
 */
class Submission extends Claim
{
    use HasId;

    public static function claimKey(): string
    {
        return Claim::SUBMISSION;
    }
}
