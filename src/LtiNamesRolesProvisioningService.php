<?php

namespace Packback\Lti1p3;

use Packback\Lti1p3\Interfaces\ILtiServiceConnector;

class LtiNamesRolesProvisioningService
{
    private $service_connector;
    private $service_data;

    public function __construct(ILtiServiceConnector $service_connector, array $service_data)
    {
        $this->service_connector = $service_connector;
        $this->service_data = $service_data;
    }

    public function getMembers()
    {
        return $this->service_connector->getAll(
            $this->service_data['context_memberships_url'],
            [LtiConstants::NRPS_SCOPE_MEMBERSHIP_READONLY],
            LtiServiceConnector::CONTENT_TYPE_MEMBERSHIPCONTAINER
        );
    }
}
