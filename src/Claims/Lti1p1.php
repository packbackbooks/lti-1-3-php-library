<?php

namespace Packback\Lti1p3\Claims;

/**
 * Lti1p1 Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti/claim/lti1p1
 *
 * Example payload:
 * {
 *     "https://purl.imsglobal.org/spec/lti/claim/lti1p1": {
 *         "user_id": "34212",
 *         "oauth_consumer_key": "179248902",
 *         "oauth_consumer_key_sign": "lWd54kFo5qU7xshAna6v8BwoBm6tmUjc6GTax6+12ps="
 *     }
 * }
 */
class Lti1p1 extends Claim
{
    public static function claimKey(): string
    {
        return Claim::LTI1P1;
    }

    public function userId(): ?string
    {
        return $this->getBody()['user_id'] ?? null;
    }

    public function oauthConsumerKeySign(): ?string
    {
        return $this->getBody()['oauth_consumer_key_sign'] ?? null;
    }

    public function oauthConsumerKey(): ?string
    {
        return $this->getBody()['oauth_consumer_key'] ?? null;
    }
}
