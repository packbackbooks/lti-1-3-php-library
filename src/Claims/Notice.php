<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\Claims\Concerns\HasId;

/**
 * Notice Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti/claim/notice
 *
 * Example payload:
 * {
 *     "https://purl.imsglobal.org/spec/lti/claim/notice": {
 *         "id": "74376c5d-1f93-491d-b96d-5173938f6e98",
 *         "timestamp": "2025-05-21T18:41:03Z",
 *         "type": "LtiContextCopyNotice"
 *     }
 * }
 */
class Notice extends Claim
{
    use HasId;

    public static function claimKey(): string
    {
        return Claim::NOTICE;
    }

    public function type(): string
    {
        return $this->getBody()['type'];
    }

    public function timestamp(): string
    {
        return $this->getBody()['timestamp'];
    }
}
