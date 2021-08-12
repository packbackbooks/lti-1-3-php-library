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
        $members = [];

        $next_page = $this->service_data['context_memberships_url'];

        while ($next_page) {
            $page = $this->service_connector->get(
                $next_page,
                [LtiConstants::NRPS_SCOPE_MEMBERSHIP_READONLY],
                LtiServiceConnector::CONTENT_TYPE_MEMBERSHIPCONTAINER
            );

            $members = array_merge($members, $page['body']['members']);

            $next_page = false;
            foreach ($page['headers'] as $header) {
                if (preg_match(LtiServiceConnector::NEXT_PAGE_REGEX, $header, $matches)) {
                    $next_page = $matches[1];
                    break;
                }
            }
        }

        return $members;
    }
}
