<?php

namespace Packback\Lti1p3;

class LtiDeepLinkResourceIcon
{
    public function __construct(
        private string $url,
        private int $width,
        private int $height
    ) {
    }

    public static function new(string $url, int $width, int $height): self
    {
        return new LtiDeepLinkResourceIcon($url, $width, $height);
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setWidth(int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function setHeight(int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'width' => $this->width,
            'height' => $this->height,
        ];
    }
}
