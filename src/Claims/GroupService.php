<?php

namespace Packback\Lti1p3\Claims;

/**
 * GroupService Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti-gs/claim/groupsservice
 *
 * No example found in test data.
 */
class GroupService extends Claim
{
    public static function claimKey(): string
    {
        return Claim::GS_GROUPSSERVICE;
    }
}
