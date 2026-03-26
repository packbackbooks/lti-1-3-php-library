<?php

namespace Packback\Lti1p3\Claims;

/**
 * Roles Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti/claim/roles
 *
 * Example payload:
 * {
 *     "https://purl.imsglobal.org/spec/lti/claim/roles": [
 *         "http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor"
 *     ]
 * }
 */
class Roles extends Claim
{
    public static function claimKey(): string
    {
        return Claim::ROLES;
    }
}
