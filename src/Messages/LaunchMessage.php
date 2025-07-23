<?php

namespace Packback\Lti1p3\Messages;

use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;

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
}
