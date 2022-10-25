<?php

namespace BNSoftware\Lti1p3;

class LtiAssignmentsGradesService extends LtiAbstractService
{
    public const CONTENTTYPE_SCORE = 'application/vnd.ims.lis.v1.score+json';
    public const CONTENTTYPE_LINEITEM = 'application/vnd.ims.lis.v2.lineitem+json';
    public const CONTENTTYPE_LINEITEMCONTAINER = 'application/vnd.ims.lis.v2.lineitemcontainer+json';
    public const CONTENTTYPE_RESULTCONTAINER = 'application/vnd.ims.lis.v2.resultcontainer+json';

    public function getScope(): array
    {
        return $this->getServiceData()['scope'];
    }

    // https://www.imsglobal.org/spec/lti-ags/v2p0#assignment-and-grade-service-claim
    // When an LTI message is launching a resource associated to one and only one lineitem,
    // the claim must include the endpoint URL for accessing the associated line item;
    // in all other cases, this property must be either blank or not included in the claim.
    public function getResourceLaunchLineItem(): ?LtiLineItem
    {
        $serviceData = $this->getServiceData();
        if (empty($serviceData['lineitem'])) {
            return null;
        }

        return LtiLineItem::new()->setId($serviceData['lineitem']);
    }

    public function putGrade(LtiGrade $grade, LtiLineItem $lineitem = null)
    {
        if (!in_array(LtiConstants::AGS_SCOPE_SCORE, $this->getScope())) {
            throw new LtiException('Missing required scope', 1);
        }

        $lineitem = $this->ensureLineItemExists($lineitem);

        $scoreUrl = $lineitem->getId();

        // Place '/scores' before url params
        $pos = strpos($scoreUrl, '?');
        $scoreUrl = $pos === false ? $scoreUrl.'/scores' : substr_replace($scoreUrl, '/scores', $pos, 0);

        $request = new ServiceRequest(
            ServiceRequest::METHOD_POST,
            $scoreUrl,
            ServiceRequest::TYPE_SYNC_GRADE
        );
        $request->setBody($grade);
        $request->setContentType(static::CONTENTTYPE_SCORE);

        return $this->makeServiceRequest($request);
    }

    public function findLineItem(LtiLineItem $newLineItem): ?LtiLineItem
    {
        $lineitems = $this->getLineItems();

        foreach ($lineitems as $lineitem) {
            if ($this->isMatchingLineItem($lineitem, $newLineItem)) {
                return new LtiLineItem($lineitem);
            }
        }

        return null;
    }

    public function updateLineItem(LtiLineItem $lineitemToUpdate): LtiLineItem
    {
        $request = new ServiceRequest(
            ServiceRequest::METHOD_PUT,
            $this->getServiceData()['lineitems'],
            ServiceRequest::TYPE_UPDATE_LINEITEM
        );

        $request->setBody($lineitemToUpdate)
            ->setContentType(static::CONTENTTYPE_LINEITEM)
            ->setAccept(static::CONTENTTYPE_LINEITEM);

        $updatedLineItem = $this->makeServiceRequest($request);

        return new LtiLineItem($updatedLineItem['body']);
    }

    public function createLineItem(LtiLineItem $newLineItem): LtiLineItem
    {
        $request = new ServiceRequest(
            ServiceRequest::METHOD_POST,
            $this->getServiceData()['lineitems'],
            ServiceRequest::TYPE_CREATE_LINEITEM
        );
        $request->setBody($newLineItem)
            ->setContentType(static::CONTENTTYPE_LINEITEM)
            ->setAccept(static::CONTENTTYPE_LINEITEM);
        $createdLineItem = $this->makeServiceRequest($request);

        return new LtiLineItem($createdLineItem['body']);
    }

    public function findOrCreateLineItem(LtiLineItem $newLineItem): LtiLineItem
    {
        return $this->findLineItem($newLineItem) ?? $this->createLineItem($newLineItem);
    }

    public function getGrades(LtiLineItem $lineitem = null)
    {
        $lineitem = $this->ensureLineItemExists($lineitem);
        $resultsUrl = $lineitem->getId();

        // Place '/results' before url params
        $pos = strpos($resultsUrl, '?');
        $resultsUrl = $pos === false ? $resultsUrl.'/results' : substr_replace($resultsUrl, '/results', $pos, 0);

        $request = new ServiceRequest(
            ServiceRequest::METHOD_GET,
            $resultsUrl,
            ServiceRequest::TYPE_GET_GRADES
        );
        $request->setAccept(static::CONTENTTYPE_RESULTCONTAINER);
        $scores = $this->makeServiceRequest($request);

        return $scores['body'];
    }

    public function getLineItems(): array
    {
        if (!in_array(LtiConstants::AGS_SCOPE_LINEITEM, $this->getScope())) {
            throw new LtiException('Missing required scope', 1);
        }

        $request = new ServiceRequest(
            ServiceRequest::METHOD_GET,
            $this->getServiceData()['lineitems'],
            ServiceRequest::TYPE_GET_LINEITEMS
        );
        $request->setAccept(static::CONTENTTYPE_LINEITEMCONTAINER);

        $lineitems = $this->getAll($request);

        // If there is only one item, then wrap it in an array so the foreach works
        if (isset($lineitems['body']['id'])) {
            $lineitems['body'] = [$lineitems['body']];
        }

        return $lineitems;
    }

    public function getLineItem(string $url): LtiLineItem
    {
        if (!in_array(LtiConstants::AGS_SCOPE_LINEITEM, $this->getScope())) {
            throw new LtiException('Missing required scope', 1);
        }

        $request = new ServiceRequest(
            ServiceRequest::METHOD_GET,
            $url,
            ServiceRequest::TYPE_GET_LINEITEM
        );
        $request->setAccept(static::CONTENTTYPE_LINEITEM);

        $response = $this->makeServiceRequest($request)['body'];

        return new LtiLineItem($response);
    }

    private function ensureLineItemExists(LtiLineItem $lineitem = null): LtiLineItem
    {
        // If no line item is passed in, attempt to use the one associated with
        // this launch.
        if (!isset($lineitem)) {
            $lineitem = $this->getResourceLaunchLineItem();
        }

        // If none exists still, create a default line item.
        if (!isset($lineitem)) {
            $defaultLineItem = LtiLineItem::new()
                ->setLabel('default')
                ->setScoreMaximum(100);
            $lineitem = $this->createLineItem($defaultLineItem);
        }

        // If the line item does not contain an ID, find or create it.
        if (empty($lineitem->getId())) {
            $lineitem = $this->findOrCreateLineItem($lineitem);
        }

        return $lineitem;
    }

    private function isMatchingLineItem(array $lineitem, LtiLineItem $newLineItem): bool
    {
        return $newLineItem->getTag() == ($lineitem['tag'] ?? null) &&
            $newLineItem->getResourceId() == ($lineitem['resourceId'] ?? null) &&
            $newLineItem->getResourceLinkId() == ($lineitem['resourceLinkId'] ?? null);
    }
}
