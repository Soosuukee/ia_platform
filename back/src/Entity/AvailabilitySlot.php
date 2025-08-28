<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

use DateTimeImmutable;

class AvailabilitySlot
{
    private int $id;
    private int $providerId;
    private DateTimeImmutable $startTime;
    private DateTimeImmutable $endTime;
    private bool $isBooked;

    public function __construct(int $providerId, DateTimeImmutable $startTime, DateTimeImmutable $endTime, bool $isBooked = false)
    {
        $this->validateTimes($startTime, $endTime);

        $this->providerId = $providerId;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->isBooked = $isBooked;
    }

    private function validateTimes(DateTimeImmutable $startTime, DateTimeImmutable $endTime): void
    {
        if ($startTime >= $endTime) {
            throw new \InvalidArgumentException('L\'heure de début doit être antérieure à l\'heure de fin');
        }

        if ($startTime < new DateTimeImmutable()) {
            throw new \InvalidArgumentException('L\'heure de début ne peut pas être dans le passé');
        }
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


    public function setStartTime(DateTimeImmutable $startTime): void
    {
        $this->validateTimes($startTime, $this->endTime);
        $this->startTime = $startTime;
    }

    public function setEndTime(DateTimeImmutable $endTime): void
    {
        $this->validateTimes($this->startTime, $endTime);
        $this->endTime = $endTime;
    }

    public function setIsBooked(bool $isBooked): void
    {
        $this->isBooked = $isBooked;
    }
}
