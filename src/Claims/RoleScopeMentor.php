<?php

namespace Packback\Lti1p3\Claims;

/**
 * RoleScopeMentor Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti/claim/role_scope_mentor
 *
 * Example payload:
 * {
 *     "https://purl.imsglobal.org/spec/lti/claim/role_scope_mentor": [
 *         "fad5fb29-a91c-770-3c110-1e687120efd9",
 *         "5d7373de-c76c-e2b-01214-69e487e2bd33",
 *         "d779cfd4-bc7b-019-9bf1a-04bf1915d4d0"
 *     ]
 * }
 */
class RoleScopeMentor extends Claim
{
    public static function claimKey(): string
    {
        return Claim::ROLE_SCOPE_MENTOR;
    }
}
