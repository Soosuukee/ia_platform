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
    private ?string $profilePicture = null;
    private DateTimeImmutable $joinedAt;
    private string $slug;
    private int $countryId;
    private string $city;
    private ?string $state = null;
    private ?string $postalCode = null;
    private ?string $address = null;

    public function __construct(
        string $firstName,
        string $lastName,
        string $email,
        string $password,
        int $countryId,
        string $city,
        ?string $profilePicture = null,
        ?string $slug = null,
        ?string $state = null,
        ?string $postalCode = null,
        ?string $address = null
    ) {
        $this->firstName = trim($firstName);
        $this->lastName = trim($lastName);
        $this->email = trim($email);
        $this->password = $password;
        $this->countryId = $countryId;
        $this->city = trim($city);
        $this->state = $state ? trim($state) : null;
        $this->postalCode = $postalCode ? trim($postalCode) : null;
        $this->address = $address ? trim($address) : null;
        $this->profilePicture = $profilePicture;
        $this->joinedAt = new DateTimeImmutable();
        $this->slug = $slug ?? '';
    }

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

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?string $profilePicture): void
    {
        $this->profilePicture = $profilePicture;
    }

    public function getJoinedAt(): DateTimeImmutable
    {
        return $this->joinedAt;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getCountryId(): int
    {
        return $this->countryId;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    // Méthodes requises par AccountHolder
    public function verifyPassword(string $password): bool
    {
        return false; // À implémenter selon vos besoins
    }



    public function getRole(): string
    {
        return 'client';
    }



    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->joinedAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'email' => $this->email,
            'profilePicture' => $this->profilePicture,
            'joinedAt' => $this->joinedAt->format('Y-m-d\TH:i:s'),
            'slug' => $this->slug,
            'countryId' => $this->countryId,
            'city' => $this->city,
            'state' => $this->state,
            'postalCode' => $this->postalCode,
            'address' => $this->address,
            'role' => $this->getRole(),
        ];
    }
}
