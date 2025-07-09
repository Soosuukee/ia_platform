<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

use DateTimeImmutable;


class User
{
    private int $id;
    private string $email;
    private string $password;
    private string $role; // "user" ou "client"
    private \DateTimeImmutable $createdAt;

    public function __construct(string $email, string $password, string $role = 'user')
    {
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        $this->createdAt = new \DateTimeImmutable();
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    // Setters
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
    }
}
