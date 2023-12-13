<?php

namespace Packback\Lti1p3;

class LtiDeployment
{
    public function __construct(
        private string $deployment_id
    ) {
    }

    public static function new($deployment_id): LtiDeployment
    {
        return new LtiDeployment($deployment_id);
    }

    public function getDeploymentId(): string
    {
        return $this->deployment_id;
    }

    public function setDeploymentId(string $deployment_id): LtiDeployment
    {
        $this->deployment_id = $deployment_id;

        return $this;
    }
}
