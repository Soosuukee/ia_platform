<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

use DateTimeImmutable;

class Article
{
    private int $id = 0;
    private int $providerId;
    private int $languageId;
    private string $title;
    private string $slug;
    private DateTimeImmutable $publishedAt;
    private string $summary;
    private bool $isPublished;
    private bool $isFeatured;
    private ?string $cover = null;

    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(
        int $providerId,
        int $languageId,
        string $title,
        string $summary,
        ?string $slug = null,
        bool $isPublished = false,
        bool $isFeatured = false,
        ?string $cover = null
    ) {
        $this->providerId = $providerId;
        $this->languageId = $languageId;
        $this->title = trim($title);
        $this->summary = trim($summary);
        $this->slug = $slug ?? '';
        $this->isPublished = $isPublished;
        $this->isFeatured = $isFeatured;
        $this->cover = $cover;
        $this->publishedAt = $isPublished ? new DateTimeImmutable() : new DateTimeImmutable('1970-01-01');
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getProviderId(): int
    {
        return $this->providerId;
    }

    public function getLanguageId(): int
    {
        return $this->languageId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = trim($title);
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getPublishedAt(): DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function getSummary(): string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): void
    {
        $this->summary = trim($summary);
        $this->updatedAt = new DateTimeImmutable();
    }

    public function isPublished(): bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): void
    {
        $this->isPublished = $isPublished;
        $this->updatedAt = new DateTimeImmutable();
        if ($isPublished) {
            $this->publishedAt = new DateTimeImmutable();
        }
    }

    public function isFeatured(): bool
    {
        return $this->isFeatured;
    }

    public function setIsFeatured(bool $isFeatured): void
    {
        $this->isFeatured = $isFeatured;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(?string $cover): void
    {
        $this->cover = $cover;
    }



    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'providerId' => $this->providerId,
            'languageId' => $this->languageId,
            'title' => $this->title,
            'slug' => $this->slug,
            'publishedAt' => $this->publishedAt->format('Y-m-d\TH:i:s'),
            'summary' => $this->summary,
            'isPublished' => $this->isPublished,
            'isFeatured' => $this->isFeatured,
            'cover' => $this->cover,
            'updatedAt' => $this->updatedAt?->format('Y-m-d\TH:i:s'),
        ];
    }
}
