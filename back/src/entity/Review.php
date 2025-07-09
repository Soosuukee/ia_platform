<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

use DateTime;
use Soosuuke\IaPlatform\Entity\User;
use Soosuuke\IaPlatform\Entity\Provider;

class Review
{
    private int $id;
    private User $user;
    private Provider $provider;
    private string $content;
    private int $rating; // Note sur 5
    private DateTime $createdAt;

    public function __construct(User $user, Provider $provider, string $content, int $rating)
    {
        $this->user = $user;
        $this->provider = $provider;
        $this->content = $content;
        $this->rating = $rating;
        $this->createdAt = new DateTime();
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getProvider(): Provider
    {
        return $this->provider;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getRating(): int
    {
        return $this->rating;
    }

    public function getCreatedAt(): DateTime
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

    public function setProvider(Provider $provider): void
    {
        $this->provider = $provider;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}
