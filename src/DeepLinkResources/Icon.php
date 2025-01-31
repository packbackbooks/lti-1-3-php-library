<?php

namespace Packback\Lti1p3\DeepLinkResources;

use Packback\Lti1p3\Concerns\Arrayable;
use Packback\Lti1p3\Concerns\NewChainable;

class Icon
{
    use Arrayable;
    use HasDimensions;
    use NewChainable;

    public function __construct(
        private string $url,
        private int $width,
        private int $height
    ) {}

    public function getArray(): array
    {
        return [
            'url' => $this->url,
            'width' => $this->width,
            'height' => $this->height,
        ];
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
}
