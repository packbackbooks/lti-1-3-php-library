<?php

namespace Packback\Lti1p3\Claims;

use Packback\Lti1p3\Claims\Concerns\HasScope;

/**
 * GroupService Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti-gs/claim/groupsservice
 *
 * Example payload:
 * {
 *     "https://purl.imsglobal.org/spec/lti-gs/claim/groupsservice": {
 *         "scope": [
 *             "https://purl.imsglobal.org/spec/lti-gs/scope/contextgroup.readonly"
 *         ],
 *         "context_groups_url": "https://www.myuniv.example.com/2344/groups",
 *         "context_group_sets_url": "https://www.myuniv.example.com/2344/groups/sets",
 *         "service_versions": ["1.0"]
 *     }
 * }
 */
class GroupService extends Claim
{
    use HasScope;

    public static function claimKey(): string
    {
        return Claim::GS_GROUPSSERVICE;
    }

    public function contextGroupsUrl(): string
    {
        return $this->getBody()['context_groups_url'];
    }

    public function contextGroupSetsUrl(): ?string
    {
        return $this->getBody()['context_group_sets_url'] ?? null;
    }

    public function serviceVersions(): array
    {
        return $this->getBody()['service_versions'];
    }
}
