<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

use DateTimeImmutable;

class ProviderDiploma
{
    private int $id;
    private string $title;
    private string $institution;
    private ?string $description;
    private ?\DateTimeImmutable $startDate;
    private ?\DateTimeImmutable $endDate;
    private int $providerId;

    public function __construct(
        string $title,
        string $institution,
        ?string $description,
        ?\DateTimeImmutable $startDate,
        ?\DateTimeImmutable $endDate,
        int $providerId
    ) {
        $this->title = $title;
        $this->institution = $institution;
        $this->description = $description;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->providerId = $providerId;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getInstitution(): string
    {
        return $this->institution;
    }

    public function setInstitution(string $institution): void
    {
        $this->institution = $institution;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeImmutable $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeImmutable $endDate): void
    {
        $this->endDate = $endDate;
    }

    public function getProviderId(): int
    {
        return $this->providerId;
    }

    public function setProviderId(int $providerId): void
    {
        $this->providerId = $providerId;
    }
}
