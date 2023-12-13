<?php

namespace Packback\Lti1p3\Interfaces;

interface IDatabase
{
    public function findRegistrationByIssuer(string $iss, ?string $clientId = null);

    public function findDeployment(string $iss, string $deploymentId, ?string $clientId = null);
}
