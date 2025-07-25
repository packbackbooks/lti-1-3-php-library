<?php

namespace Packback\Lti1p3\Claims;

/**
 * Lis Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti/claim/lis
 *
 * Example payload:
 * {
 *     "https://purl.imsglobal.org/spec/lti/claim/lis": {
 *         "person_sourcedid": null,
 *         "course_offering_sourcedid": null,
 *         "validation_context": null,
 *         "errors": {
 *             "errors": {}
 *         },
 *     }
 * }
 */
class Lis extends Claim
{
    public static function claimKey(): string
    {
        return Claim::LIS;
    }
}
