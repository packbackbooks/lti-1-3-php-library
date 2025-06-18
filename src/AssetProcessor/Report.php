<?php

namespace Packback\Lti1p3\AssetProcessor;

use Packback\Lti1p3\Concerns\Arrayable;

class Report
{
    use Arrayable;
    private ?string $title;
    private ?string $indicationAlt;
    private ?string $indicationColor;
    private ?float $scoreGiven;
    private ?float $scoreMaximum;
    private ?string $errorCode;

    public function __construct(
        private $assetId,
        private string $type,
        private string $processingProgress,
        private int $priority,
        private string $timestamp,
    ) {}

    public static function new(
        string $assetId,
        string $type,
        string $processingProgress,
        int $priority,
        string $timestamp,
    ): self {
        return new Report($assetId, $type, $processingProgress, $priority, $timestamp);
    }

    public function getArray(): array
    {
        return [
            'assetId' => $this->assetId,
            'type' => $this->type,
            'processingProgress' => $this->processingProgress,
            'priority' => $this->priority,
            'timestamp' => $this->timestamp,
            'errorCode' => $this->errorCode,
            'indicationAlt' => $this->indicationAlt,
            'indicationColor' => $this->indicationColor,
            'scoreGiven' => $this->scoreGiven,
            'scoreMaximum' => $this->scoreMaximum,
            'title' => $this->title,
        ];
    }

    public function getAssetId()
    {
        return $this->assetId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getProcessingProgress(): string
    {
        return $this->processingProgress;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getTimestamp(): string
    {
        return $this->timestamp;
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

    public function getIndicationAlt(): ?string
    {
        return $this->indicationAlt;
    }

    public function setIndicationAlt(?string $value): self
    {
        $this->indicationAlt = $value;

        return $this;
    }

    public function getIndicationColor(): ?string
    {
        return $this->indicationColor;
    }

    public function setIndicationColor(?string $value): self
    {
        $this->indicationColor = $value;

        return $this;
    }

    public function getScoreGiven(): ?float
    {
        return $this->scoreGiven;
    }

    public function setScoreGiven(?float $value): self
    {
        $this->scoreGiven = $value;

        return $this;
    }

    public function getScoreMaximum(): ?float
    {
        return $this->scoreMaximum;
    }

    public function setScoreMaximum(?float $value): self
    {
        $this->scoreMaximum = $value;

        return $this;
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    public function setErrorCode(?string $value): self
    {
        $this->errorCode = $value;

        return $this;
    }
}
