<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

use DateTimeImmutable;
use Soosuuke\IaPlatform\Entity\Enum\BookingStatus;

class Booking
{
    private int $id;
    private BookingStatus $status;
    private int $clientId;
    private int $slotId;
    private DateTimeImmutable $createdAt;

    public function __construct(string $status, int $clientId, int $slotId)
    {
        $this->status = BookingStatus::fromString($status);
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
        return $this->status->value;
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
        $this->status = BookingStatus::fromString($status);
    }
}
