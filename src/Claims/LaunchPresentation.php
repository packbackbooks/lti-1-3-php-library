<?php

namespace Packback\Lti1p3\Claims;

/**
 * LaunchPresentation Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti/claim/launch_presentation
 *
 * Example payload:
 * {
 *     "https://purl.imsglobal.org/spec/lti/claim/launch_presentation": {
 *         "document_target": "window",
 *         "height": 360,
 *         "width": 480
 *     }
 * }
 */
class LaunchPresentation extends Claim
{
    public static function claimKey(): string
    {
        return Claim::LAUNCH_PRESENTATION;
    }

    public function documentTarget(): string
    {
        return $this->getBody()['document_target'];
    }

    public function height(): int
    {
        return $this->getBody()['height'];
    }

    public function width(): int
    {
        return $this->getBody()['width'];
    }

    public function returnUrl(): ?string
    {
        return $this->getBody()['return_url'];
    }
}
