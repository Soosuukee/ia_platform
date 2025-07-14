<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

use DateTimeImmutable;


class Review
{
    private int $id;
    private int $clientId;
    private int $providerId;
    private string $content;
    private int $rating; // Note sur 5
    private DateTimeImmutable $createdAt;

    public function __construct(int $clientId, int $providerId, string $content, int $rating)
    {
        $this->clientId = $clientId;
        $this->providerId = $providerId;
        $this->content = $content;
        $this->rating = $rating;
        $this->createdAt = new DateTimeImmutable();
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getClientId(): int
    {
        return $this->clientId;
    }

    public function getProviderId(): int
    {
        return $this->providerId;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getRating(): int
    {
        return $this->rating;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    // Setters
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function setRating(int $rating): void
    {
        $this->rating = $rating;
    }
}
