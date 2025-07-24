<?php

namespace Packback\Lti1p3\Claims;

/**
 * ForUser Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti/claim/for_user
 *
 * Example payload:
 * {
 *     "https://purl.imsglobal.org/spec/lti/claim/for_user": {
 *         "user_id": "a839e110-eea3-4ebe-88db-f817d161a4f2"
 *     }
 * }
 */
class ForUser extends Claim
{
    public static function claimKey(): string
    {
        return Claim::FOR_USER;
    }

    public function userId()
    {
        return $this->getBody()['user_id'];
    }
}
