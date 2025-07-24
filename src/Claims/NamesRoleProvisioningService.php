<?php

namespace Packback\Lti1p3\Claims;

/**
 * NamesRoleProvisioningService Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti-nrps/claim/namesroleservice
 *
 * Example payload:
 * {
 *     "https://purl.imsglobal.org/spec/lti-nrps/claim/namesroleservice": {
 *         "context_memberships_url": "https://ltiadvantagevalidator.imsglobal.org/ltitool/namesandroles.html?memberships=1879",
 *         "service_versions": [
 *             "2.0"
 *         ]
 *     }
 * }
 */
class NamesRoleProvisioningService extends Claim
{
    public static function claimKey(): string
    {
        return Claim::NRPS_NAMESROLESSERVICE;
    }

    public function contextMembershipsUrl(): string
    {
        return $this->getBody()['context_memberships_url'];
    }

    public function serviceVersions(): array
    {
        return $this->getBody()['service_versions'];
    }
}
