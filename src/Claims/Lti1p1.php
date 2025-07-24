<?php

namespace Packback\Lti1p3\Claims;

/**
 * Lti1p1 Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti/claim/lti1p1
 *
 * No example found in test data.
 */
class Lti1p1 extends Claim
{
    public static function claimKey(): string
    {
        return Claim::LTI1P1;
    }

    public function getOauthConsumerKeySign(): ?string
    {
        return $this->getBody()['oauth_consumer_key_sign'] ?? null;
    }
}
