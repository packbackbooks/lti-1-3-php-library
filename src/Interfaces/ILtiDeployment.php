<?php

namespace Packback\Lti1p3\Interfaces;

/** @internal */
interface ILtiDeployment
{
    public function getDeploymentId(): string;

    public function setDeploymentId(string $deployment_id): ILtiDeployment;
}
