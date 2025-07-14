<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

class CompletedWorkMedia
{
    private int $id;
    private int $workId;
    private string $mediaType;
    private string $mediaUrl;

    public function __construct(int $workId, string $mediaType, string $mediaUrl)
    {
        $this->workId = $workId;
        $this->mediaType = $mediaType;
        $this->mediaUrl = $mediaUrl;
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getWorkId(): int
    {
        return $this->workId;
    }

    public function getMediaType(): string
    {
        return $this->mediaType;
    }

    public function getMediaUrl(): string
    {
        return $this->mediaUrl;
    }

    // Setters
    public function setWorkId(int $workId): void
    {
        $this->workId = $workId;
    }

    public function setMediaType(string $mediaType): void
    {
        $this->mediaType = $mediaType;
    }

    public function setMediaUrl(string $mediaUrl): void
    {
        $this->mediaUrl = $mediaUrl;
    }
}
