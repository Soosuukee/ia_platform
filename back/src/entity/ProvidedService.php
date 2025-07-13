<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

class ProvidedService
{
    private int $id;
    private string $title;
    private string $description;
    private ?int  $minPrice;
    private ?int  $maxPrice;
    private string $duration;
    private int $providerId;

    public function __construct(
        int $id,
        string $title,
        string $description,
        ?int $minPrice,
        ?int $maxPrice,
        string $duration,
        int $providerId
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->minPrice = $minPrice;
        $this->maxPrice = $maxPrice;
        $this->duration = $duration;
        $this->providerId = $providerId;
    }

    // --- Getters ---
    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getMinPrice(): ?int
    {
        return $this->minPrice;
    }

    public function getMaxPrice(): ?int
    {
        return $this->maxPrice;
    }

    public function getDuration(): string
    {
        return $this->duration;
    }

    public function getProviderId(): int
    {
        return $this->providerId;
    }

    // --- Setters ---
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setMinPrice(?int $minPrice): void
    {
        $this->minPrice = $minPrice;
    }

    public function setMaxPrice(?int $maxPrice): void
    {
        $this->maxPrice = $maxPrice;
    }

    public function setDuration(string $duration): void
    {
        $this->duration = $duration;
    }

    public function setProviderId(int $providerId): void
    {
        $this->providerId = $providerId;
    }
}
