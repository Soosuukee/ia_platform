<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

use DateTimeImmutable;

class Experience
{
    private int $id;
    private int $providerId;
    private string $title;
    private string $companyName;
    private string $firstTask;
    private ?string $secondTask = null;
    private ?string $thirdTask = null;
    private DateTimeImmutable $startedAt;
    private ?DateTimeImmutable $endedAt;
    private ?string $companyLogo = null;

    public function __construct(
        int $providerId,
        string $title,
        string $companyName,
        string $firstTask,
        DateTimeImmutable $startedAt,
        ?DateTimeImmutable $endedAt = null,
        ?string $secondTask = null,
        ?string $thirdTask = null,
        ?string $companyLogo = null
    ) {
        $this->providerId = $providerId;
        $this->title = trim($title);
        $this->companyName = trim($companyName);
        $this->firstTask = trim($firstTask);
        $this->startedAt = $startedAt;
        $this->endedAt = $endedAt;
        $this->secondTask = $secondTask ? trim($secondTask) : null;
        $this->thirdTask = $thirdTask ? trim($thirdTask) : null;
        $this->companyLogo = $companyLogo;
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

    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    public function getFirstTask(): string
    {
        return $this->firstTask;
    }

    public function getSecondTask(): ?string
    {
        return $this->secondTask;
    }

    public function getThirdTask(): ?string
    {
        return $this->thirdTask;
    }

    public function getStartedAt(): DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function getEndedAt(): ?DateTimeImmutable
    {
        return $this->endedAt;
    }

    public function getCompanyLogo(): ?string
    {
        return $this->companyLogo;
    }

    public function setCompanyLogo(?string $companyLogo): void
    {
        $this->companyLogo = $companyLogo;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'providerId' => $this->providerId,
            'title' => $this->title,
            'companyName' => $this->companyName,
            'firstTask' => $this->firstTask,
            'secondTask' => $this->secondTask,
            'thirdTask' => $this->thirdTask,
            'startedAt' => $this->startedAt->format('Y-m-d'),
            'endedAt' => $this->endedAt?->format('Y-m-d'),
            'companyLogo' => $this->companyLogo,
        ];
    }
}
