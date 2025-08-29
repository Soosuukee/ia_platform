<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

use DateTimeImmutable;

class CompletedWork
{
    private int $id;
    private int $providerId;
    private string $company;
    private string $title;
    private string $description;
    private DateTimeImmutable $startDate;
    private ?DateTimeImmutable $endDate;

    public function __construct(
        int $providerId,
        string $company,
        string $title,
        string $description,
        DateTimeImmutable $startDate,
        ?DateTimeImmutable $endDate = null
    ) {
        $this->providerId = $providerId;
        $this->company = $company;
        $this->title = $title;
        $this->description = $description;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
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

    public function getCompany(): string
    {
        return $this->company;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getStartDate(): DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): ?DateTimeImmutable
    {
        return $this->endDate;
    }

    // Setters
    public function setCompany(string $company): void
    {
        $this->company = $company;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setStartDate(DateTimeImmutable $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function setEndDate(?DateTimeImmutable $endDate): void
    {
        $this->endDate = $endDate;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'providerId' => $this->providerId,
        ];
    }
}
