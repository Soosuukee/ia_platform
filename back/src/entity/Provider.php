<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

use Soosuuke\IaPlatform\Entity\User;
use Soosuuke\IaPlatform\Entity\AvailabilitySlot;
use DateTimeImmutable;

class Provider
{
    private int $id;
    private User $user;
    private string $bio;
    private DateTimeImmutable $createdAt;

    /**
     * @var AvailabilitySlot[]
     */
    private array $availabilitySlots = [];

    public function __construct(User $user, string $bio)
    {
        $this->user = $user;
        $this->bio = $bio;
        $this->createdAt = new DateTimeImmutable();
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

    public function getBio(): string
    {
        return $this->bio;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return AvailabilitySlot[]
     */
    public function getAvailabilitySlots(): array
    {
        return $this->availabilitySlots;
    }

    // Setters
    public function setBio(string $bio): void
    {
        $this->bio = $bio;
    }

    public function setAvailabilitySlots(array $slots): void
    {
        $this->availabilitySlots = $slots;
    }

    public function addAvailabilitySlot(AvailabilitySlot $slot): void
    {
        $this->availabilitySlots[] = $slot;
    }
}
