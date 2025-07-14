<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

use DateTimeImmutable;

class CompletedWork
{
    private int $id;
    private int $providerId;
    private string $title;
    private string $description;
    private DateTimeImmutable $completedAt;

    public function __construct(int $providerId, string $title, string $description)
    {
        $this->providerId = $providerId;
        $this->title = $title;
        $this->description = $description;
        $this->completedAt = new DateTimeImmutable();
    }

    // Getters
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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCompletedAt(): DateTimeImmutable
    {
        return $this->completedAt;
    }

    // Setters (si tu veux les garder)
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setCompletedAt(DateTimeImmutable $completedAt): void
    {
        $this->completedAt = $completedAt;
    }
}
