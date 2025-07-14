<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

use DateTimeImmutable;
use Soosuuke\IaPlatform\Contract\AccountHolder;

class Client implements AccountHolder
{
    private int $id;
    private string $firstName;
    private string $lastName;
    private string $email;
    private string $password;
    private string $country;
    private string $role;
    private DateTimeImmutable $createdAt;

    public function __construct(
        string $firstName,
        string $lastName,
        string $email,
        string $password,
        string $country = 'unknown'
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->password = $password;
        $this->country = $country;
        $this->role = 'client';
        $this->createdAt = new DateTimeImmutable();
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }


    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    // Setters

    public function getSetFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getSetLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setCountry(string $country): void
    {
        $this->country = $country;
    }
}
