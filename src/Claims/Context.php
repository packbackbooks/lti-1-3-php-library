<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\Claims\Concerns\HasId;

/**
 * Context Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti/claim/context
 *
 * Example payload:
 * {
 *     "https://purl.imsglobal.org/spec/lti/claim/context": {
 *         "id": "8893483",
 *         "label": "Biology 102",
 *         "title": "Bio Adventures",
 *         "type": [
 *             "http://purl.imsglobal.org/vocab/lis/v2/course#CourseSection"
 *         ]
 *     }
 * }
 */
class Context extends Claim
{
    use HasId;

    public static function claimKey(): string
    {
        return Claim::CONTEXT;
    }

    public function label()
    {
        return $this->getBody()['label'];
    }

    public function title()
    {
        return $this->getBody()['title'];
    }

    public function type(): array
    {
        return $this->getBody()['type'];
    }
}
