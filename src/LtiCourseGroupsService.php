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
        $groups = [];

        $next_page = $this->service_data['context_groups_url'];

        while ($next_page) {
            $page = $this->service_connector->get(
                $next_page,
                $this->service_data['scope'],
                LtiServiceConnector::CONTENT_TYPE_CONTEXTGROUPCONTAINER,
            );

            $groups = array_merge($groups, $page['body']['groups']);

            $next_page = false;
            foreach ($page['headers'] as $header) {
                if (preg_match(LtiServiceConnector::NEXT_PAGE_REGEX, $header, $matches)) {
                    $next_page = $matches[1];
                    break;
                }
            }
        }

        return $groups;
    }

    public function getSets()
    {
        $sets = [];

        // Sets are optional.
        if (!isset($this->service_data['context_group_sets_url'])) {
            return [];
        }

        $next_page = $this->service_data['context_group_sets_url'];

        while ($next_page) {
            $page = $this->service_connector->makeServiceRequest(
                LtiServiceConnector::METHOD_GET,
                $this->service_data['scope'],
                $next_page,
                LtiServiceConnector::CONTENT_TYPE_CONTEXTGROUPCONTAINER,
            );

            $sets = array_merge($sets, $page['body']['sets']);

            $next_page = false;
            foreach ($page['headers'] as $header) {
                if (preg_match(LtiServiceConnector::NEXT_PAGE_REGEX, $header, $matches)) {
                    $next_page = $matches[1];
                    break;
                }
            }
        }

        return $sets;
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
