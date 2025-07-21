<?php

namespace Packback\Lti1p3\Messages;

use Packback\Lti1p3\Interfaces\ILtiRegistration;

abstract class LaunchMessage extends LtiMessage
{
    protected string $launchId;

    public function __construct(
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
        return $this->hasClaim(LtiConstants::PNS_CLAIM_SERVICE);
    }

    /**
     * Fetches an instance of the platform notification service for the current launch.
     */
    public function getPns(): PlatformNotificationService
    {
        return new PlatformNotificationService(
            $this->getBody()[LtiConstants::PNS_CLAIM_SERVICE]
        );
    }
    /**
     * Returns whether or not the current launch can use the names and roles service.
     */
    public function hasNrps(): bool
    {
        return $this->hasClaim(LtiConstants::NRPS_CLAIM_SERVICE);
    }

    /**
     * Fetches an instance of the names and roles service for the current launch.
     */
    public function getNrps(): LtiNamesRolesProvisioningService
    {
        return new LtiNamesRolesProvisioningService(
            $this->serviceConnector,
            $this->registration,
            $this->jwt['body'][LtiConstants::NRPS_CLAIM_SERVICE]
        );
    }
    /**
     * Returns whether or not the current launch can use the groups service.
     */
    public function hasGs(): bool
    {
        return $this->hasClaim(LtiConstants::GS_CLAIM_SERVICE);
    }

    /**
     * Fetches an instance of the groups service for the current launch.
     */
    public function getGs(): LtiCourseGroupsService
    {
        return new LtiCourseGroupsService(
            $this->serviceConnector,
            $this->registration,
            $this->jwt['body'][LtiConstants::GS_CLAIM_SERVICE]
        );
    }
    /**
     * Returns whether or not the current launch can use the assignments and grades service.
     */
    public function hasAgs(): bool
    {
        return $this->hasClaim(LtiConstants::AGS_CLAIM_ENDPOINT);
    }

    /**
     * Fetches an instance of the assignments and grades service for the current launch.
     */
    public function getAgs(): LtiAssignmentsGradesService
    {
        return new LtiAssignmentsGradesService(
            $this->serviceConnector,
            $this->registration,
            $this->jwt['body'][LtiConstants::AGS_CLAIM_ENDPOINT]
        );
    }
}
