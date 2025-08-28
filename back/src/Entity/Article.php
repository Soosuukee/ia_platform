<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

use DateTimeImmutable;

class Article
{
    private int $id;
    private int $providerId;
    private string $title;
    private string $slug;
    private DateTimeImmutable $publishedAt;
    private string $summary;
    private bool $isPublished;
    private bool $isFeatured;
    private ?string $cover = null;
    private string $tag;
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(
        int $providerId,
        string $title,
        string $summary,
        string $tag,
        ?string $slug = null,
        bool $isPublished = false,
        bool $isFeatured = false,
        ?string $cover = null
    ) {
        $this->providerId = $providerId;
        $this->title = trim($title);
        $this->summary = trim($summary);
        $this->tag = trim($tag);
        $this->slug = $slug ?? '';
        $this->isPublished = $isPublished;
        $this->isFeatured = $isFeatured;
        $this->cover = $cover;
        $this->publishedAt = $isPublished ? new DateTimeImmutable() : new DateTimeImmutable('1970-01-01');
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getProviderId(): int
    {
        return $this->providerId;
    }

    public function getTitle(): string
    {
        return $this->title;
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

    public function isPublished(): bool
    {
        return $this->isPublished;
    }

    public function isFeatured(): bool
    {
        return $this->isFeatured;
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(?string $cover): void
    {
        $this->cover = $cover;
    }

    public function getTag(): string
    {
        return $this->tag;
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
}
