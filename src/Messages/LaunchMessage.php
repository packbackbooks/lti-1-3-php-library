<?php

namespace Packback\Lti1p3\Messages;

abstract class LaunchMessage extends LtiMessage
{
    protected string $launchId;

    public function __construct(protected array $body)
    {
        $this->launchId = uniqid('lti1p3_launch_', true);
    }

    public function getLaunchId(): string
    {
        return $this->launchId;
    }
}
