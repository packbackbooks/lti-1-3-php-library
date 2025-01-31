<?php

namespace Packback\Lti1p3\DeepLinkResources;

use Packback\Lti1p3\Concerns\Arrayable;
use Packback\Lti1p3\Concerns\NewChainable;

/** @phpstan-consistent-constructor */
class Iframe
{
    use Arrayable;
    use HasDimensions;
    use NewChainable;

    public function __construct(
        private ?string $src = null,
        private ?int $width = null,
        private ?int $height = null
    ) {}

    public function getArray(): array
    {
        return [
            'width' => $this->width,
            'height' => $this->height,
            'src' => $this->src,
        ];
    }

    public function setSrc(?string $src): self
    {
        $this->src = $src;

        return $this;
    }

    public function getSrc(): ?string
    {
        return $this->src;
    }
}
