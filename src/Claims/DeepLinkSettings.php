<?php

namespace Packback\Lti1p3\Claims;

/**
 * DeepLinkSettings Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti-dl/claim/deep_linking_settings
 *
 * Example payload:
 * {
 *     "https://purl.imsglobal.org/spec/lti-dl/claim/deep_linking_settings": {
 *         "accept_types": [
 *             "link",
 *             "file",
 *             "html",
 *             "ltiResourceLink"
 *         ],
 *         "accept_media_types": "image/*,text/html,application/json",
 *         "accept_presentation_document_targets": [
 *             "iframe",
 *             "window"
 *         ],
 *         "accept_multiple": true,
 *         "auto_create": true,
 *         "title": "Certification Deep Linking",
 *         "text": "Certification Default Text Description",
 *         "data": "909708",
 *         "deep_link_return_url": "https://ltiadvantagevalidator.imsglobal.org/ltitool/deeplinkresponse.html"
 *     }
 * }
 */
class DeepLinkSettings extends Claim
{
    public static function claimKey(): string
    {
        return Claim::DL_DEEP_LINK_SETTINGS;
    }

    public function acceptTypes(): array
    {
        return $this->getBody()['accept_types'];
    }

    public function canAcceptType(string $acceptType): bool
    {
        return in_array($acceptType, $this->acceptTypes());
    }

    public function acceptMediaTypes(): ?string
    {
        return $this->getBody()['accept_media_types'] ?? null;
    }

    public function acceptPresentationDocumentTargets(): array
    {
        return $this->getBody()['accept_presentation_document_targets'];
    }

    public function canAcceptPresentationDocumentTarget(string $target): bool
    {
        return in_array($target, $this->acceptPresentationDocumentTargets());
    }

    public function acceptLineitem(): bool
    {
        return $this->getBody()['accept_lineitem'] ?? false;
    }

    public function acceptMultiple(): bool
    {
        return $this->getBody()['accept_multiple'] ?? false;
    }

    public function autoCreate(): bool
    {
        return $this->getBody()['auto_create'] ?? false;
    }

    public function title(): ?string
    {
        return $this->getBody()['title'] ?? null;
    }

    public function text(): ?string
    {
        return $this->getBody()['text'] ?? null;
    }

    public function data(): ?string
    {
        return $this->getBody()['data'] ?? null;
    }

    public function deepLinkReturnUrl(): string
    {
        return $this->getBody()['deep_link_return_url'];
    }
}
