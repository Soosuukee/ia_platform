<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

use DateTimeImmutable;
use Soosuuke\IaPlatform\Entity\ValueObject\Rating;

class Review
{
    private int $id;
    private int $clientId;
    private int $providerId;
    private string $comment;
    private Rating $rating;
    private DateTimeImmutable $createdAt;

    public function __construct(int $clientId, int $providerId, string $comment, int $rating)
    {
        if (empty(trim($comment))) {
            throw new \InvalidArgumentException('Le commentaire ne peut pas être vide');
        }

        $this->clientId = $clientId;
        $this->providerId = $providerId;
        $this->comment = trim($comment);
        $this->rating = new Rating($rating);
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

    public function getComment(): string
    {
        return $this->comment;
    }

    public function getRating(): int
    {
        return $this->rating->getValue();
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    // Setters
    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    public function setRating(int $rating): void
    {
        $this->rating = new Rating($rating);
    }
}
