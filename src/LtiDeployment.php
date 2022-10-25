<?php

namespace BNSoftware\Lti1p3;

class LtiDeployment
{
    private $deploymentId;

    /**
     * @return LtiDeployment
     */
    public static function new(): LtiDeployment
    {
        return new LtiDeployment();
    }

    /**
     * @return mixed
     */
    public function getDeploymentId()
    {
        return $this->deploymentId;
    }

    /**
     * @param $deploymentId
     * @return LtiDeployment
     */
    public function setDeploymentId($deploymentId): LtiDeployment
    {
        $this->deploymentId = $deploymentId;

        return $this;
    }
}
