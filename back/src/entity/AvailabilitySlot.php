<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

use Soosuuke\IaPlatform\Entity\Provider;
use DateTimeImmutable;

class AvailabilitySlot
{
    private int $id;
    private Provider $provider;
    private DateTimeImmutable $startTime;
    private DateTimeImmutable $endTime;
    private bool $isBooked;

    public function __construct(Provider $provider, DateTimeImmutable $startTime, DateTimeImmutable $endTime, bool $isBooked)
    {
        $this->provider = $provider;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->isBooked = false;
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

    public function getStartTime(): DateTimeImmutable
    {
        return $this->startTime;
    }

    public function getEndTime(): DateTimeImmutable
    {
        return $this->endTime;
    }

    public function isBooked(): bool
    {
        return $this->isBooked;
    }

    // Setters
    public function setProvider(Provider $provider): void
    {
        $this->provider = $provider;
    }

    public function setStartTime(DateTimeImmutable $startTime): void
    {
        $this->startTime = $startTime;
    }

    public function setEndTime(DateTimeImmutable $endTime): void
    {
        $this->endTime = $endTime;
    }

    public function setIsBooked(bool $isBooked): void
    {
        $this->isBooked = $isBooked;
    }
}
