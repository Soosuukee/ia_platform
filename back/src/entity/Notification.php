<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

use DateTimeImmutable;
use Soosuuke\IaPlatform\Entity\User;

class Notification
{
    private int $id;
    private User $recipient;
    private string $message;
    private bool $isRead;
    private DateTimeImmutable $createdAt;

    public function __construct(User $recipient, string $message)
    {
        $this->recipient = $recipient;
        $this->message = $message;
        $this->isRead = false;
        $this->createdAt = new DateTimeImmutable();
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }
    public function getRecipient(): User
    {
        return $this->recipient;
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
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
    public function markAsRead(): void
    {
        $this->isRead = true;
    }
}
