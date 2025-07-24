<?php

namespace Packback\Lti1p3\Claims;

/**
 * DeploymentId Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti/claim/deployment_id
 *
 * Example payload:
 * {
 *     "https://purl.imsglobal.org/spec/lti/claim/deployment_id": "testdeploy"
 * }
 */
class DeploymentId extends Claim
{
    public static function claimKey(): string
    {
        return Claim::DEPLOYMENT_ID;
    }
}
