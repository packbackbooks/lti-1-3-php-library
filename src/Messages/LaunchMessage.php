<?php

namespace Packback\Lti1p3\Messages;

use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\LtiAssignmentsGradesService;
use Packback\Lti1p3\LtiCourseGroupsService;
use Packback\Lti1p3\LtiNamesRolesProvisioningService;

abstract class LaunchMessage extends LtiMessage
{
    protected string $launchId;

    public function __construct(
        protected ILtiServiceConnector $serviceConnector,
        protected ILtiRegistration $registration,
        protected array $body
    ) {
        $this->launchId = uniqid('lti1p3_launch_', true);
    }

    public function getLaunchId(): string
    {
        return $this->launchId;
    }

    /**
     * @todo deprecate
     */
    public function getLaunchData(): array
    {
        return $this->body;
    }

    /**
     * Returns whether or not the current launch can use the assignments and grades service.
     */
    public function hasPns(): bool
    {
        return $this->hasClaim(Claim::PLATFORMNOTIFICATIONSERVICE);
    }

    /**
     * Fetches an instance of the names and roles service for the current launch.
     */
    public function getNrps(): LtiNamesRolesProvisioningService
    {
        return new LtiNamesRolesProvisioningService(
            $this->serviceConnector,
            $this->registration,
            $this->getClaim(Claim::NRPS_NAMESROLESSERVICE)->getBody()
        );
    }
    /**
     * Returns whether or not the current launch can use the groups service.
     */
    public function hasGs(): bool
    {
        return $this->hasClaim(Claim::GS_GROUPSSERVICE);
    }

    /**
     * Fetches an instance of the groups service for the current launch.
     */
    public function getGs(): LtiCourseGroupsService
    {
        return new LtiCourseGroupsService(
            $this->serviceConnector,
            $this->registration,
            $this->getClaim(Claim::GS_GROUPSSERVICE)->getBody()
        );
    }
    /**
     * Returns whether or not the current launch can use the assignments and grades service.
     */
    public function hasAgs(): bool
    {
        return $this->hasClaim(Claim::AGS_ENDPOINT);
    }

    /**
     * Fetches an instance of the assignments and grades service for the current launch.
     */
    public function getAgs(): LtiAssignmentsGradesService
    {
        return new LtiAssignmentsGradesService(
            $this->serviceConnector,
            $this->registration,
            $this->getClaim(Claim::AGS_ENDPOINT)->getBody()
        );
    }
}
