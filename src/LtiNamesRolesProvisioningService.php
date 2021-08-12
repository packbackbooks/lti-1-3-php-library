<?php

namespace Packback\Lti1p3;

use Packback\Lti1p3\Interfaces\ILtiServiceConnector;

class LtiNamesRolesProvisioningService
{
    private $serviceConnector;
    private $serviceData;

    public function __construct(ILtiServiceConnector $serviceConnector, array $serviceData)
    {
        $this->serviceConnector = $serviceConnector;
        $this->serviceData = $serviceData;
    }

    public function getMembers()
    {
        return $this->serviceConnector->getAll(
            $this->serviceData['context_memberships_url'],
            [LtiConstants::NRPS_SCOPE_MEMBERSHIP_READONLY],
            LtiServiceConnector::CONTENT_TYPE_MEMBERSHIPCONTAINER
        );
    }
}
