<?php

namespace Packback\Lti1p3;

use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Interfaces\ICache;
use Packback\Lti1p3\Interfaces\ICookie;
use Packback\Lti1p3\Interfaces\IDatabase;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\Messages\LaunchMessage;
use Packback\Lti1p3\MessageValidators\AssetProcessorSettingsValidator;
use Packback\Lti1p3\MessageValidators\DeepLinkMessageValidator;
use Packback\Lti1p3\MessageValidators\EulaMessageValidator;
use Packback\Lti1p3\MessageValidators\ReportReviewMessageValidator;
use Packback\Lti1p3\MessageValidators\ResourceMessageValidator;
use Packback\Lti1p3\MessageValidators\SubmissionReviewMessageValidator;
use Packback\Lti1p3\PlatformNotificationService\PlatformNotificationService;

class AbstractedLtiMessageLaunch // extends LaunchMessage
{
    #[\Deprecated(message: 'use LtiConstants::MESSAGE_TYPE_DEEPLINK instead', since: '6.4')]
    public const TYPE_DEEPLINK = LtiConstants::MESSAGE_TYPE_DEEPLINK;

    #[\Deprecated(message: 'use LtiConstants::MESSAGE_TYPE_SUBMISSIONREVIEW instead', since: '6.4')]
    public const TYPE_SUBMISSIONREVIEW = LtiConstants::MESSAGE_TYPE_SUBMISSIONREVIEW;

    #[\Deprecated(message: 'use LtiConstants::MESSAGE_TYPE_RESOURCE instead', since: '6.4')]
    public const TYPE_RESOURCELINK = LtiConstants::MESSAGE_TYPE_RESOURCE;

    /**
     * Static function to allow for method chaining without having to assign to a variable first.
     */
    public static function new(
        IDatabase $db,
        ICache $cache,
        ICookie $cookie,
        ILtiServiceConnector $serviceConnector
    ): self {
        return new LtiMessageLaunch($db, $cache, $cookie, $serviceConnector);
    }

    /**
     * Load an LtiMessageLaunch from a Cache using a launch id.
     *
     * @throws LtiException Will throw an LtiException if validation fails or launch cannot be found
     */
    public static function fromCache(
        string $launch_id,
        IDatabase $db,
        ICache $cache,
        ICookie $cookie,
        ILtiServiceConnector $serviceConnector
    ): self {
        $new = new LtiMessageLaunch($db, $cache, $cookie, $serviceConnector);
        $new->launch_id = $launch_id;
        $new->jwt = ['body' => $new->cache->getLaunchData($launch_id)];

        return $new->validateRegistration();
    }

    #[\Deprecated(message: 'use setMessage() instead', since: '6.4')]
    public function setRequest(array $request): static
    {
        return $this->setMessage($request);
    }

    /**
     * Returns whether or not the current launch can use the names and roles service.
     */
    public function hasNrps(): bool
    {
        return isset($this->getBody()[Claim::NRPS_NAMESROLESSERVICE]['context_memberships_url']);
    }

    /**
     * Fetches an instance of the names and roles service for the current launch.
     */
    public function getNrps(): LtiNamesRolesProvisioningService
    {
        return new LtiNamesRolesProvisioningService(
            $this->serviceConnector,
            $this->registration,
            $this->getBody()[Claim::NRPS_NAMESROLESSERVICE]
        );
    }

    /**
     * Returns whether or not the current launch can use the groups service.
     */
    public function hasGs(): bool
    {
        return isset($this->getBody()[Claim::GS_GROUPSSERVICE]['context_groups_url']);
    }

    /**
     * Fetches an instance of the groups service for the current launch.
     */
    public function getGs(): LtiCourseGroupsService
    {
        return new LtiCourseGroupsService(
            $this->serviceConnector,
            $this->registration,
            $this->getBody()[Claim::GS_GROUPSSERVICE]
        );
    }

    /**
     * Returns whether or not the current launch can use the assignments and grades service.
     */
    public function hasAgs(): bool
    {
        return isset($this->getBody()[Claim::AGS_ENDPOINT]);
    }

    /**
     * Fetches an instance of the assignments and grades service for the current launch.
     */
    public function getAgs(): LtiAssignmentsGradesService
    {
        return new LtiAssignmentsGradesService(
            $this->serviceConnector,
            $this->registration,
            $this->getBody()[Claim::AGS_ENDPOINT]
        );
    }

    /**
     * Returns whether or not the current launch can use the assignments and grades service.
     */
    public function hasPns(): bool
    {
        return isset($this->getBody()[Claim::PLATFORMNOTIFICATIONSERVICE]);
    }

    /**
     * Fetches an instance of the platform notification service for the current launch.
     */
    public function getPns(): PlatformNotificationService
    {
        return new PlatformNotificationService(
            $this->getBody()[Claim::PLATFORMNOTIFICATIONSERVICE]
        );
    }

    public static function messageType(): string
    {
        return '';
    }

    public function isMessageType(string $type): bool
    {
        return $this->getBody()[Claim::MESSAGE_TYPE] === $type;
    }

    /**
     * Returns whether or not the current launch is a deep linking launch.
     */
    public function isDeepLinkLaunch(): bool
    {
        return $this->isMessageType(LtiConstants::MESSAGE_TYPE_DEEPLINK);
    }

    /**
     * Fetches a deep link that can be used to construct a deep linking response.
     */
    public function getDeepLink(): LtiDeepLink
    {
        return new LtiDeepLink(
            $this->registration,
            $this->getBody()[Claim::DEPLOYMENT_ID],
            $this->getBody()[Claim::DL_DEEP_LINK_SETTINGS]
        );
    }

    /**
     * Returns whether or not the current launch is a submission review launch.
     */
    public function isSubmissionReviewLaunch(): bool
    {
        return $this->isMessageType(LtiConstants::MESSAGE_TYPE_SUBMISSIONREVIEW);
    }

    /**
     * Returns whether or not the current launch is a resource launch.
     */
    public function isResourceLaunch(): bool
    {
        return $this->isMessageType(LtiConstants::MESSAGE_TYPE_RESOURCE);
    }

    /**
     * Returns whether or not the current launch is a EULA launch.
     */
    public function isEulaLaunch(): bool
    {
        return $this->isMessageType(LtiConstants::MESSAGE_TYPE_EULA);
    }

    /**
     * Returns whether or not the current launch is a Report Review launch.
     */
    public function isReportReviewLaunch(): bool
    {
        return $this->isMessageType(LtiConstants::MESSAGE_TYPE_REPORTREVIEW);
    }

    /**
     * Returns whether or not the current launch is an Asset Processor Settings launch.
     */
    public function isAssetProcessorSettingsLaunch(): bool
    {
        return $this->isMessageType(LtiConstants::MESSAGE_TYPE_ASSETPROCESSORSETTINGS);
    }

    protected function messageValidator(): string
    {
        $availableValidators = [
            DeepLinkMessageValidator::class,
            ReportReviewMessageValidator::class,
            ResourceMessageValidator::class,
            SubmissionReviewMessageValidator::class,
            EulaMessageValidator::class,
            AssetProcessorSettingsValidator::class,
        ];

        // Filter out validators that cannot validate the message
        $applicableValidators = array_filter($availableValidators, function ($validator) {
            return $validator::canValidate($this->getBody());
        });

        // There should be 0-1 validators. This will either return the validator, or null if none apply.
        return array_shift($applicableValidators);
    }

    protected function getMessageValidator(array $jwtBody): ?string
    {
        return $this->messageValidator();
    }
}
