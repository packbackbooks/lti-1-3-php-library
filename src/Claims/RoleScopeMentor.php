<?php

namespace Packback\Lti1p3\Claims;

/**
 * RoleScopeMentor Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti/claim/role_scope_mentor
 *
 * No example found in test data.
 */
class RoleScopeMentor extends Claim
{
    public static function claimKey(): string
    {
        return Claim::ROLE_SCOPE_MENTOR;
    }
}
