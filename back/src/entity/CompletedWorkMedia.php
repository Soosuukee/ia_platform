<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

use Soosuuke\IaPlatform\Entity\CompletedWork;

class CompletedWorkMedia
{
    private int $id;
    private CompletedWork $work;
    private string $mediaType;
    private string $mediaUrl;

    public function __construct(CompletedWork $work, string $mediaType, string $mediaUrl)
    {
        $this->work = $work;
        $this->mediaType = $mediaType;
        $this->mediaUrl = $mediaUrl;
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getWork(): CompletedWork
    {
        return $this->work;
    }

    public function getMediaType(): string
    {
        return $this->mediaType;
    }

    public function getMediaUrl(): string
    {
        return $this->mediaUrl;
    }

    // Setters (si tu veux pouvoir modifier)
    public function setMediaType(string $mediaType): void
    {
        $this->mediaType = $mediaType;
    }

    public function setMediaUrl(string $mediaUrl): void
    {
        $this->mediaUrl = $mediaUrl;
    }
}
