<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

use DateTimeImmutable;

class Service
{
    private int $id;
    private int $providerId;
    private DateTimeImmutable $createdAt;
    private ?float $maxPrice;
    private ?float $minPrice;
    private bool $isActive;
    private bool $isFeatured;
    private ?string $cover = null;
    private string $summary;
    private string $tag;
    private string $slug;

    public function __construct(
        int $providerId,
        string $summary,
        string $tag,
        ?float $maxPrice = null,
        ?float $minPrice = null,
        bool $isActive = true,
        bool $isFeatured = false,
        ?string $cover = null,
        ?string $slug = null
    ) {
        $this->providerId = $providerId;
        $this->createdAt = new DateTimeImmutable();
        $this->maxPrice = $maxPrice;
        $this->minPrice = $minPrice;
        $this->isActive = $isActive;
        $this->isFeatured = $isFeatured;
        $this->cover = $cover;
        $this->summary = trim($summary);
        $this->tag = trim($tag);
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

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(?string $cover): void
    {
        $this->cover = $cover;
    }

    public function getSummary(): string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): void
    {
        $this->summary = trim($summary);
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function setTag(string $tag): void
    {
        $this->tag = trim($tag);
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }
}
