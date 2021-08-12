<?php

namespace Packback\Lti1p3;

use Packback\Lti1p3\Interfaces\ILtiServiceConnector;

class LtiCourseGroupsService
{
    private $service_connector;
    private $service_data;

    public function __construct(ILtiServiceConnector $service_connector, array $service_data)
    {
        $this->service_connector = $service_connector;
        $this->service_data = $service_data;
    }

    public function getGroups()
    {
        return $this->service_connector->getAll(
            $this->service_data['context_groups_url'],
            $this->service_data['scope'],
            LtiServiceConnector::CONTENT_TYPE_CONTEXTGROUPCONTAINER,
        );
    }

    public function getSets()
    {
        return $this->service_connector->getAll(
            $this->service_data['context_group_sets_url'],
            $this->service_data['scope'],
            LtiServiceConnector::CONTENT_TYPE_CONTEXTGROUPCONTAINER,
        );
    }

    public function getGroupsBySet()
    {
        $groups = $this->getGroups();
        $sets = $this->getSets();

        $groups_by_set = [];
        $unsetted = [];

        foreach ($sets as $key => $set) {
            $groups_by_set[$set['id']] = $set;
            $groups_by_set[$set['id']]['groups'] = [];
        }

        foreach ($groups as $key => $group) {
            if (isset($group['set_id']) && isset($groups_by_set[$group['set_id']])) {
                $groups_by_set[$group['set_id']]['groups'][$group['id']] = $group;
            } else {
                $unsetted[$group['id']] = $group;
            }
        }

        if (!empty($unsetted)) {
            $groups_by_set['none'] = [
                'name' => 'None',
                'id' => 'none',
                'groups' => $unsetted,
            ];
        }

        return $groups_by_set;
    }
}
