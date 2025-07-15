<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

use DateTimeImmutable;

class Booking
{
    private int $id;
    private string $status; // pending, accepted, declined
    private int $clientId;
    private int $slotId;
    private DateTimeImmutable $createdAt;

    public function __construct(string $status, int $clientId, int $slotId)
    {
        $this->status = $status;
        $this->clientId = $clientId;
        $this->slotId = $slotId;
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

    public function getClientId(): int
    {
        return $this->clientId;
    }

    public function getSlotId(): int
    {
        return $this->slotId;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    // Setters
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function setClientId(int $clientId): void
    {
        $this->clientId = $clientId;
    }

    public function setSlotId(int $slotId): void
    {
        $this->slotId = $slotId;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
