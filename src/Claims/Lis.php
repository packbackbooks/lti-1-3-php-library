<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\Claims\Concerns\HasErrors;

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
    use HasErrors;

    public static function claimKey(): string
    {
        return Claim::LIS;
    }

    public function personSourcedId(): ?string
    {
        return $this->getBody()['person_sourcedid'] ?? null;
    }

    public function courseOfferingSourcedId(): ?string
    {
        return $this->getBody()['course_offering_sourcedid'] ?? null;
    }

    public function validationContext(): ?array
    {
        return $this->getBody()['validation_context'] ?? null;
    }
}
