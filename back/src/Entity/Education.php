<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

use DateTimeImmutable;

class Education
{
    private int $id;
    private int $providerId;
    private string $title;
    private string $institutionName;
    private string $description;
    private DateTimeImmutable $startedAt;
    private ?DateTimeImmutable $endedAt;
    private ?string $institutionImage = null;

    public function __construct(
        int $providerId,
        string $title,
        string $institutionName,
        string $description,
        DateTimeImmutable $startedAt,
        ?DateTimeImmutable $endedAt = null,
        ?string $institutionImage = null
    ) {
        $this->providerId = $providerId;
        $this->title = trim($title);
        $this->institutionName = trim($institutionName);
        $this->description = trim($description);
        $this->startedAt = $startedAt;
        $this->endedAt = $endedAt;
        $this->institutionImage = $institutionImage;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getProviderId(): int
    {
        return $this->providerId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getInstitutionName(): string
    {
        return $this->institutionName;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getStartedAt(): DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function getEndedAt(): ?DateTimeImmutable
    {
        return $this->endedAt;
    }

    public function getInstitutionImage(): ?string
    {
        return $this->institutionImage;
    }

    public function setInstitutionImage(?string $institutionImage): void
    {
        $this->institutionImage = $institutionImage;
    }
}
