<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

use Soosuuke\IaPlatform\Entity\User;

class Provider
{
    private int $id;
    private User $user;
    private string $bio;
    private string $availability;
    private \DateTime $createdAt;

    public function __construct(User $user, string $bio, string $availability)
    {
        $this->user = $user;
        $this->bio = $bio;
        $this->availability = $availability;
        $this->createdAt = new \DateTime();
    }


    // Getters
    public function getId(): int
    {
        return $this->id;
    }



    public function getBio(): string
    {
        return $this->bio;
    }

    public function getAvailability(): string
    {
        return $this->availability;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    // Setters
    public function setBio(string $bio): void
    {
        $this->bio = $bio;
    }

    public function setAvailability(string $availability): void
    {
        $this->availability = $availability;
    }
}
