<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

use Soosuuke\IaPlatform\Entity\User;
use Soosuuke\IaPlatform\Entity\AvailabilitySlot;
use DateTimeImmutable;

class Booking
{
    private int $id;
    private string $status; // pending, accepted, declined
    private User $client;
    private AvailabilitySlot $slot;
    private DateTimeImmutable $createdAt;

    public function __construct(string $status, User $client, AvailabilitySlot $slot)
    {
        $this->status = $status;
        $this->client = $client;
        $this->slot = $slot;
        $this->createdAt = new DateTimeImmutable();
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getClient(): User
    {
        return $this->client;
    }

    public function getSlot(): AvailabilitySlot
    {
        return $this->slot;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    // Setters

    public function setStatus(string $status): void
    {
        $this->status = 'pending';
    }
    public function setClient(User $client): void
    {
        $this->client = $client;
    }

    public function setSlot(AvailabilitySlot $slot): void
    {
        $this->slot = $slot;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
