<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

use DateTimeImmutable;

class Notification
{
    private int $id;
    private int $recipientId;
    private string $recipientType; // 'client' ou 'provider'
    private string $message;
    private bool $isRead;
    private DateTimeImmutable $createdAt;

    public function __construct(int $recipientId, string $recipientType, string $message)
    {
        $this->recipientId = $recipientId;
        $this->recipientType = $recipientType;
        $this->message = $message;
        $this->isRead = false;
        $this->createdAt = new DateTimeImmutable();
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getRecipientId(): int
    {
        return $this->recipientId;
    }

    public function getRecipientType(): string
    {
        return $this->recipientType;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function isRead(): bool
    {
        return $this->isRead;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    // Setters


    public function markAsRead(): void
    {
        $this->isRead = true;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'recipientId' => $this->recipientId,
        ];
    }
}
