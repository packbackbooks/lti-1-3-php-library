<?php

namespace Packback\Lti1p3\Messages;

use Firebase\JWT\JWT;
use Packback\Lti1p3\Claims\DeploymentId;
use Packback\Lti1p3\Claims\Version;
use Packback\Lti1p3\Concerns\Claimable;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;

abstract class LtiMessage
{
    use Claimable;

    abstract public static function requiredClaims(): array;
    // abstract public static function optionalClaims(): array;
    abstract public static function messageValidator(): string;

    public function __construct(
        protected ILtiServiceConnector $serviceConnector,
        protected ILtiRegistration $registration,
        protected array $body
    ) {}

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

    public function validate(): static
    {
        static::messageValidator()::validate($this->getBody());

        return $this;
    }

    public function hasClaim(string $claim): bool
    {
        return static::hasClaimInBody($claim, $this->body);
    }

    public function claimDeploymentId(): DeploymentId
    {
        return DeploymentId::create($this);
    }

    public function claimVersion(): Version
    {
        return Version::create($this);
    }
}
