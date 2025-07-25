<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\Claims\Concerns\HasScope;

/**
 * AssignmentGradeService Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti-ags/claim/endpoint
 *
 * Example payload:
 * {
 *     "https://purl.imsglobal.org/spec/lti-ags/claim/endpoint": {
 *         "scope": [
 *             "https://purl.imsglobal.org/spec/lti-ags/scope/lineitem",
 *             "https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly",
 *             "https://purl.imsglobal.org/spec/lti-ags/scope/score"
 *         ],
 *         "lineitems": "https://ltiadvantagevalidator.imsglobal.org/ltitool/rest/assignmentsgrades/17387/lineitems"
 *     }
 * }
 */
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
