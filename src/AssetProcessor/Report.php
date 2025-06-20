<?php

namespace Packback\Lti1p3\AssetProcessor;

use Packback\Lti1p3\Concerns\Arrayable;

class Report
{
    use Arrayable;
    private ?string $title = null;

    /**
     * Alternate text representing the meaning of the indicationColor for screen readers or as a tooltip over the indication color.
     */
    private ?string $indicationAlt = null;

    /**
     * A hex (#RRGGBB) color
     */
    private ?string $indicationColor = null;
    private ?string $result = null;
    private ?float $scoreGiven = null;
    private ?float $scoreMaximum = null;

    /**
     * One of: UNSUPPORTED_ASSET_TYPE, ASSET_TOO_LARGE, ASSET_TOO_SMALL, EULA_NOT_ACCEPTED, DOWNLOAD_FAILED
     */
    private ?string $errorCode = null;

    /**
     * Human-readable explanation of the error code
     */
    private ?string $comment = null;

    /**
     * @param  string  $processingProgress  One of: Processed, Processing, PendingManual, Failed, NotProcessed, NotReady
     * @param  int  $priority  A number from 0 (meaning "good" or "success") to 5 (meaning urgent or time-critical notable features) indicating the tool's perceived priority of the report.
     */
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
            'result' => $this->result,
            'scoreGiven' => $this->scoreGiven,
            'scoreMaximum' => $this->scoreMaximum,
            'title' => $this->title,
            'comment' => $this->comment,
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

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $value): self
    {
        $this->comment = $value;

        return $this;
    }

    public function getIndicationAlt(): ?string
    {
        return $this->indicationAlt;
    }

    public function setIndicationAlt(string $value): self
    {
        $this->indicationAlt = $value;

        return $this;
    }

    public function getIndicationColor(): ?string
    {
        return $this->indicationColor;
    }

    public function setIndicationColor(string $value): self
    {
        $this->indicationColor = $value;

        return $this;
    }

    public function getResult(): ?string
    {
        return $this->result;
    }

    public function setResult(string $value): self
    {
        $this->result = $value;

        return $this;
    }

    public function getScoreGiven(): ?float
    {
        return $this->scoreGiven;
    }

    public function setScoreGiven(float $value): self
    {
        $this->scoreGiven = $value;

        return $this;
    }

    public function getScoreMaximum(): ?float
    {
        return $this->scoreMaximum;
    }

    public function setScoreMaximum(float $value): self
    {
        $this->scoreMaximum = $value;

        return $this;
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    public function setErrorCode(string $value): self
    {
        $this->errorCode = $value;

        return $this;
    }
}
