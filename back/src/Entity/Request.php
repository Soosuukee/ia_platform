<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

use DateTimeImmutable;
use Soosuuke\IaPlatform\Entity\Enum\RequestStatus;

class Request
{
    private int $id;
    private int $clientId;
    private int $providerId;
    private string $title;
    private string $description;
    private DateTimeImmutable $createdAt;
    private RequestStatus $status;

    public function __construct(int $clientId, int $providerId, string $title, string $description)
    {
        $this->validateInputs($title, $description);

        $this->clientId = $clientId;
        $this->providerId = $providerId;
        $this->title = trim($title);
        $this->description = trim($description);
        $this->createdAt = new DateTimeImmutable();
        $this->status = RequestStatus::PENDING;
    }

    private function validateInputs(string $title, string $description): void
    {
        if (empty(trim($title))) {
            throw new \InvalidArgumentException('Le titre ne peut pas être vide');
        }
        if (empty(trim($description))) {
            throw new \InvalidArgumentException('La description ne peut pas être vide');
        }
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getClientId(): int
    {
        return $this->clientId;
    }

    public function getProviderId(): int
    {
        return $this->providerId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getStatus(): string
    {
        return $this->status->value;
    }

    // Setters
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setStatus(string $status): void
    {
        $this->status = RequestStatus::fromString($status);
    }
}
