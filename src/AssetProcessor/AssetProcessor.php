<?php

namespace Packback\Lti1p3\AssetProcessor;

use Packback\Lti1p3\Concerns\Arrayable;

class AssetProcessor
{
    use Arrayable;
    private ?string $title = null;
    private ?string $text = null;
    private ?string $url = null;
    private ?array $custom = null;

    public static function new(): self
    {
        return new AssetProcessor;
    }

    public function getArray(): array
    {
        return [
            'title' => $this->title,
            'text' => $this->text,
            'url' => $this->url,
            'custom' => $this->custom,
        ];
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $value): self
    {
        $this->title = $value;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $value): self
    {
        $this->text = $value;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $value): self
    {
        $this->url = $value;

        return $this;
    }

    public function setReport(?Report $report): self
    {
        $this->report = $report;

        return $this;
    }

    public function getReport(): ?Report
    {
        return $this->report;
    }

    public function getCustom(): ?LtiCustom
    {
        return $this->custom;
    }

    public function setCustom(?LtiCustom $value): self
    {
        $this->custom = $value;

        return $this;
    }
}
