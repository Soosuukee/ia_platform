<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

use DateTimeImmutable;

class Service
{
    private int $id = 0;
    private int $providerId;
    private DateTimeImmutable $createdAt;
    private ?float $maxPrice;
    private ?float $minPrice;
    private bool $isActive;
    private bool $isFeatured;
    private ?string $cover = null;
    private string $title;
    private string $summary;
    private string $slug;

    public function __construct(
        int $providerId,
        string $title,
        ?float $maxPrice = null,
        ?float $minPrice = null,
        bool $isActive = true,
        bool $isFeatured = false,
        ?string $cover = null,
        ?string $summary = null,
        ?string $slug = null
    ) {
        $this->providerId = $providerId;
        $this->title = trim($title);
        $this->createdAt = new DateTimeImmutable();
        $this->maxPrice = $maxPrice;
        $this->minPrice = $minPrice;
        $this->isActive = $isActive;
        $this->isFeatured = $isFeatured;
        $this->cover = $cover;
        $this->summary = trim($summary ?? '');
        $this->slug = $slug ?? '';
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getProviderId(): int
    {
        return $this->providerId;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getMaxPrice(): ?float
    {
        return $this->maxPrice;
    }

    public function getMinPrice(): ?float
    {
        return $this->minPrice;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function isFeatured(): bool
    {
        return $this->isFeatured;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    public function setIsFeatured(bool $isFeatured): void
    {
        $this->isFeatured = $isFeatured;
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(?string $cover): void
    {
        $this->cover = $cover;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = trim($title);
    }

    public function getSummary(): string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): void
    {
        $this->summary = trim($summary);
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'providerId' => $this->providerId,
            'createdAt' => $this->createdAt->format('Y-m-d\TH:i:s'),
            'maxPrice' => $this->maxPrice,
            'minPrice' => $this->minPrice,
            'isActive' => $this->isActive,
            'isFeatured' => $this->isFeatured,
            'cover' => $this->cover,
            'title' => $this->title,
            'summary' => $this->summary,
            'slug' => $this->slug,
        ];
    }
}
