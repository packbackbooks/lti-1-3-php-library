<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\Claims\Concerns\HasId;

/**
 * Notice Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti/claim/notice
 *
 * No example found in test data.
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
