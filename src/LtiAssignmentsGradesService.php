<?php

namespace Packback\Lti1p3;

use Packback\Lti1p3\Interfaces\ILtiServiceConnector;

class LtiAssignmentsGradesService
{
    private $serviceConnector;
    private $serviceData;

    public function __construct(ILtiServiceConnector $serviceConnector, array $serviceData)
    {
        $this->serviceConnector = $serviceConnector;
        $this->serviceData = $serviceData;
    }

    public function putGrade(LtiGrade $grade, LtiLineitem $lineitem = null)
    {
        $this->validateScope(LtiConstants::AGS_SCOPE_SCORE, $this->serviceData['scope']);

        $scoreUrl = $this->getUrlFromLineItem($lineitem, 'scores');

        return $this->serviceConnector->post(
            $scoreUrl,
            $grade,
            $this->serviceData['scope'],
            LtiServiceConnector::CONTENT_TYPE_SCORE,
        );
    }

    public function findOrCreateLineitem(LtiLineitem $lineitem)
    {
        $foundLineItems = $this->getAllLineItems();

        $foundLineItem = $this->findMatchingLineItem($lineitem, $foundLineItems);
        if ($foundLineItem) {
            return new LtiLineitem($foundLineItem);
        }

        $this->createLineitem($lineitem);
    }

    public function getGrades(LtiLineitem $lineitem)
    {
        $lineitem = $this->findOrCreateLineitem($lineitem);

        $resultsUrl = $this->getUrlFromLineItem($lineitem, 'results');

        $results = $this->serviceConnector->get(
            $resultsUrl,
            $this->serviceData['scope'],
            LtiServiceConnector::CONTENT_TYPE_RESULTCONTAINER,
        );

        return $results['body'];
    }

    private function validateScope(string $needle, array $haystack)
    {
        if (!in_array($needle, $haystack)) {
            throw new LtiException('Missing required scope', 1);
        }
    }

    private function getLineitemUrl(LtiLineitem $lineitem = null)
    {
        if ($lineitem && empty($lineitem->getId())) {
            $lineitem = $this->findOrCreateLineitem($lineitem);

            return $lineitem->getId();
        } elseif (isset($this->serviceData['lineitem'])) {
            return $this->serviceData['lineitem'];
        } else {
            $lineitem = LtiLineitem::new()
                ->setLabel('default')
                ->setScoreMaximum(100);
            $this->createLineitem($lineitem);

            return $lineitem->getId();
        }
    }

    private function getUrlFromLineItem(LtiLineitem $lineitem = null, string $suffix = null)
    {
        $lineitemUrl = $this->getLineitemUrl($lineitem);

        // Place suffix before url params
        $position = strpos($lineitemUrl, '?');
        if ($position === false) {
            return $lineitemUrl.'/'.$suffix;
        } else {
            return substr_replace($lineitemUrl, '/'.$suffix, $position, 0);
        }
    }

    private function isMatchingLineItem(LtiLineitem $expected, array $actual)
    {
        return (
            (empty($expected->getResourceId()) && empty($expected->getResourceLinkId())) ||
            (isset($actual['resourceId']) && $actual['resourceId'] == $expected->getResourceId()) ||
            (isset($actual['resourceLinkId']) && $actual['resourceLinkId'] == $expected->getResourceLinkId())
        ) && (
            empty($expected->getTag()) || $actual['tag'] == $expected->getTag()
        );
    }

    private function findMatchingLineItem(LtiLineitem $needle, array $haystack)
    {
        foreach ($haystack as $lineitem) {
            if ($this->isMatchingLineItem($needle, $lineitem)) {
                return $lineitem;
            }
        }
    }

    private function createLineitem(LtiLineitem $lineitem)
    {
        $response = $this->serviceConnector->post(
            $this->serviceData['lineitems'],
            $lineitem,
            $this->serviceData['scope'],
            LtiServiceConnector::CONTENT_TYPE_LINEITEM,
        );

        return new LtiLineitem($response['body']);
    }

    private function getAllLineItems()
    {
        $this->validateScope(LtiConstants::AGS_SCOPE_LINEITEM, $this->serviceData['scope']);

        $line_items = $this->serviceConnector->getAll(
            $next_page,
            $this->serviceData['scope'],
            LtiServiceConnector::CONTENT_TYPE_CONTEXTGROUPCONTAINER,
        );

        // If there is only one item, then wrap it in an array so the foreach works
        if (isset($line_items['body']['id'])) {
            $line_items['body'] = [$line_items['body']];
        }

        return $line_items;
    }
}
