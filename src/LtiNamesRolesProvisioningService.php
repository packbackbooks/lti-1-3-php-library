<?php

namespace Packback\Lti1p3;

class LtiNamesRolesProvisioningService extends LtiAbstractService
{
    public const CONTENTTYPE_MEMBERSHIPCONTAINER = 'application/vnd.ims.lti-nrps.v2.membershipcontainer+json';

    public function getScope(): array
    {
        return [LtiConstants::NRPS_SCOPE_MEMBERSHIP_READONLY];
    }

    /**
     * @param  array  $options An array of options that can be passed with the context_membership_url such as rlid, since, etc.
     */
    public function getMembers(array $options = []): array
    {
        $url = $this->getServiceData()['context_memberships_url'];
        if (!empty($options)) {
            $url .= '?'.http_build_query($options);
        }
        $request = new ServiceRequest(
            ServiceRequest::METHOD_GET,
            $url,
            ServiceRequest::TYPE_GET_MEMBERSHIPS
        );
        $request->setAccept(static::CONTENTTYPE_MEMBERSHIPCONTAINER);

        return $this->getAll($request, 'members');
    }
}
