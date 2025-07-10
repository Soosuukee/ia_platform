<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

use Soosuuke\IaPlatform\Entity\Provider;
use DateTimeImmutable;

class CompletedWork
{
    private int $id;
    private Provider $provider;
    private string $title;
    private string $description;
    private DateTimeImmutable $completedAt;

    public function __construct(Provider $provider, string $title, string $description)
    {
        $this->provider = $provider;
        $this->title = $title;
        $this->description = $description;
        $this->completedAt = new DateTimeImmutable();
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
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
