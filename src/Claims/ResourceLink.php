<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\Claims\Concerns\HasId;

/**
 * ResourceLink Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti/claim/resource_link
 *
 * Example payload:
 * {
 *     "https://purl.imsglobal.org/spec/lti/claim/resource_link": {
 *         "id": "3622bed7314b4f9c8f0e533e158ff797",
 *         "title": "Introduction Assignment",
 *         "description": "This is the introduction assignment"
 *     }
 * }
 */
class ResourceLink extends Claim
{
    use HasId;

    public static function claimKey(): string
    {
        return Claim::RESOURCE_LINK;
    }

    public function title(): string
    {
        return $this->getBody()['title'];
    }

    public function description(): string
    {
        return $this->getBody()['description'];
    }
}
