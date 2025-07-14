<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

use DateTimeImmutable;

class Request
{
    private int $requestId;
    private int $clientId;
    private int $providerId;
    private string $title;
    private string $description;
    private DateTimeImmutable $createdAt;
    private string $status; // pending / accepted / declined / completed

    public function __construct(int $clientId, int $providerId, string $title, string $description)
    {
        $this->clientId = $clientId;
        $this->providerId = $providerId;
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

    public function getClientId(): int
    {
        return $this->clientId;
    }

    public function getProvider(): int
    {
        return $this->providerId;
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
