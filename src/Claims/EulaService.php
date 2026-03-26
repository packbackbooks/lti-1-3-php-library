<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\Claims\Concerns\HasScope;

/**
 * EulaService Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti/claim/eulaservice
 *
 * Example payload:
 * {
 *     "https://purl.imsglobal.org/spec/lti/claim/eulaservice": {
 *         "url": "https://platform.example.edu/api/lti/eula",
 *         "scope": [
 *             "https://purl.imsglobal.org/spec/lti/scope/eula"
 *         ]
 *     }
 * }
 */
class EulaService extends Claim
{
    use HasScope;

    public static function claimKey(): string
    {
        return Claim::EULASERVICE;
    }

    public function url()
    {
        return $this->getBody()['url'] ?? null;
    }
}
