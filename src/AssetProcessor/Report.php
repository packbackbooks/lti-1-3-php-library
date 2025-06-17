<?php

namespace Packback\Lti1p3\AssetProcessor;

use Packback\Lti1p3\Concerns\Arrayable;

class Report
{
    use Arrayable;
    private ?string $type = null;
    private ?string $title = null;
    private ?string $comment = null;
    private ?int $score_given = null;
    private ?int $score_maximum = null;
    private ?string $indication_color = null;
    private ?string $indication_alt = null;
    private ?string $error_code = null;
    private ?int $priority = null;

    public static function new(): self
    {
        return new AssetProcessor;
    }

    public function getArray(): array
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'comment' => $this->comment,
            'score_given' => $this->score_given,
            'score_maximum' => $this->score_maximum,
            'score_maximum' => $this->score_maximum,
            'indication_color' => $this->indication_color,
            'indication_alt' => $this->indication_alt,
            'error_code' => $this->error_code,
            'priority' => $this->priority,
        ];
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(?string $value): self
    {
        $this->type = $value;

        return $this;
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
