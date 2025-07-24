<?php

namespace Packback\Lti1p3\Messages;

use Firebase\JWT\JWT;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\DeploymentId;
use Packback\Lti1p3\Claims\Version;
use Packback\Lti1p3\Concerns\Claimable;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;

abstract class LtiMessage
{
    use Claimable;

    public function __construct(
        protected ILtiServiceConnector $serviceConnector,
        protected ILtiRegistration $registration,
        protected array $body
    ) {}

    abstract public static function requiredClaims(): array;

    /**
     * Fetches the decoded body of the JWT used in the current message.
     */
    public function getBody(): array
    {
        return $this->body;
    }

    public function getAud(): string
    {
        if (is_array($this->body['aud'])) {
            return $this->body['aud'][0];
        } else {
            return $this->body['aud'];
        }
    }

    /**
     * @param  class-string<Claim>  $claim
     */
    public function hasClaim(string $claim): bool
    {
        return static::hasClaimInBody($claim::claimKey(), $this->body);
    }

    public function deploymentIdClaim(): DeploymentId
    {
        return DeploymentId::create($this);
    }

    public function versionClaim(): Version
    {
        return Version::create($this);
    }
}
