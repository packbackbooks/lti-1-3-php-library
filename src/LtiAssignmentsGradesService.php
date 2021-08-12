<?php

namespace Packback\Lti1p3;

use Packback\Lti1p3\Interfaces\ILtiServiceConnector;

class LtiAssignmentsGradesService
{
    private $service_connector;
    private $service_data;

    public function __construct(ILtiServiceConnector $service_connector, array $service_data)
    {
        $this->service_connector = $service_connector;
        $this->service_data = $service_data;
    }

    public function validateScope(string $needle, array $haystack)
    {
        if (!in_array($needle, $haystack)) {
            throw new LtiException('Missing required scope', 1);
        }
    }

    public function putGrade(LtiGrade $grade, LtiLineitem $lineitem = null)
    {
        $this->validateScope(LtiConstants::AGS_SCOPE_SCORE, $this->service_data['scope']);

        if ($lineitem !== null && empty($lineitem->getId())) {
            $lineitem = $this->findOrCreateLineitem($lineitem);
            $score_url = $lineitem->getId();
        } elseif ($lineitem === null && !empty($this->service_data['lineitem'])) {
            $score_url = $this->service_data['lineitem'];
        } else {
            $lineitem = LtiLineitem::new()
                ->setLabel('default')
                ->setScoreMaximum(100);
            $lineitem = $this->findOrCreateLineitem($lineitem);
            $score_url = $lineitem->getId();
        }

        // Place '/scores' before url params
        $pos = strpos($score_url, '?');
        $score_url = $pos === false ? $score_url.'/scores' : substr_replace($score_url, '/scores', $pos, 0);

        return $this->service_connector->post(
            $score_url,
            $grade,
            $this->service_data['scope'],
            LtiServiceConnector::CONTENT_TYPE_SCORE,
        );
    }

    public function findOrCreateLineitem(LtiLineitem $new_line_item)
    {
        $line_items = $this->getLineItems();

        foreach ($line_items as $line_item) {
            if (
                (empty($new_line_item->getResourceId()) && empty($new_line_item->getResourceLinkId())) ||
                (isset($line_item['resourceId']) && $line_item['resourceId'] == $new_line_item->getResourceId()) ||
                (isset($line_item['resourceLinkId']) && $line_item['resourceLinkId'] == $new_line_item->getResourceLinkId())
            ) {
                if (empty($new_line_item->getTag()) || $line_item['tag'] == $new_line_item->getTag()) {
                    return new LtiLineitem($line_item);
                }
            }
        }
        $created_line_item = $this->service_connector->post(
            $this->service_data['lineitems'],
            $new_line_item,
            $this->service_data['scope'],
            LtiServiceConnector::CONTENT_TYPE_LINEITEM,
        );

        return new LtiLineitem($created_line_item['body']);
    }

    public function getGrades(LtiLineitem $lineitem)
    {
        $lineitem = $this->findOrCreateLineitem($lineitem);
        // Place '/results' before url params
        $pos = strpos($lineitem->getId(), '?');
        $results_url = $pos === false ? $lineitem->getId().'/results' : substr_replace($lineitem->getId(), '/results', $pos, 0);
        $scores = $this->service_connector->get(
            $results_url,
            $this->service_data['scope'],
            LtiServiceConnector::CONTENT_TYPE_RESULTCONTAINER,
        );

        return $scores['body'];
    }

    public function getLineItems()
    {
        $this->validateScope(LtiConstants::AGS_SCOPE_LINEITEM, $this->service_data['scope']);

        $line_items = [];

        $next_page = $this->service_data['lineitems'];

        while ($next_page) {
            $page = $this->service_connector->get(
                $this->service_data['scope'],
                $next_page,
                LtiServiceConnector::CONTENT_TYPE_CONTEXTGROUPCONTAINER,
            );

            $line_items = array_merge($line_items, $page['body']);
            $next_page = false;
            $link = $page['headers']['Link'];

            if (preg_match(LtiServiceConnector::NEXT_PAGE_REGEX, $link, $matches)) {
                $next_page = $matches[1];
            }
        }

        // If there is only one item, then wrap it in an array so the foreach works
        if (isset($line_items['body']['id'])) {
            $line_items['body'] = [$line_items['body']];
        }

        return $line_items;
    }
}
