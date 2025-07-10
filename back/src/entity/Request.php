<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

use DateTimeImmutable;
use Soosuuke\IaPlatform\Entity\User;
use Soosuuke\IaPlatform\Entity\Provider;

class Request
{
    private int $requestId;
    private User $user;
    private Provider $provider;
    private string $title;
    private string $description;
    private DateTimeImmutable $createdAt;
    private string $status; // pending / accepted / declined / completed

    public function __construct(User $user, Provider $provider, string $title, string $description)
    {
        $this->user = $user;
        $this->provider = $provider;
        $this->title = $title;
        $this->description = $description;
        $this->createdAt = new DateTimeImmutable();
        $this->status = 'pending';
    }

    // Getters
    public function getRequestId(): int
    {
        return $this->requestId;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getProvider(): Provider
    {
        return $this->provider;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    // Setters
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
}
