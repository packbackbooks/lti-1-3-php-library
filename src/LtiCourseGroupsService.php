<?php

namespace Packback\Lti1p3;

use Packback\Lti1p3\Interfaces\ILtiServiceConnector;

class LtiCourseGroupsService
{
    private $serviceConnector;
    private $serviceData;

    public function __construct(ILtiServiceConnector $serviceConnector, array $serviceData)
    {
        $this->serviceConnector = $serviceConnector;
        $this->serviceData = $serviceData;
    }

    public function getGroups()
    {
        return $this->serviceConnector->getAll(
            $this->serviceData['context_groups_url'],
            $this->serviceData['scope'],
            LtiServiceConnector::CONTENT_TYPE_CONTEXTGROUPCONTAINER,
        );
    }

    public function getSets()
    {
        return $this->serviceConnector->getAll(
            $this->serviceData['context_group_sets_url'],
            $this->serviceData['scope'],
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
