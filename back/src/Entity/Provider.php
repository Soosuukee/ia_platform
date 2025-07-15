<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

use DateTimeImmutable;
use Soosuuke\IaPlatform\Contract\AccountHolder;

class Provider implements AccountHolder
{
    private int $id;
    private string $firstName;
    private string $lastName;
    private string $email;
    private string $password;
    private string $country;
    private ?string $profilePicture = null;
    private string $role;
    private DateTimeImmutable $createdAt;
    private string $title;
    private string $presentation;

    /**
     * @var AvailabilitySlot[]
     */
    private array $availabilitySlots = [];

    /**
     * @var Skill[]
     */
    private array $skills = [];

    /**
     * @var string[]
     */
    private array $socialLinks = []; // Nouveau champ pour les liens sociaux

    public function __construct(
        string $firstName,
        string $lastName,
        string $email,
        string $password,
        string $title,
        string $presentation,
        string $country,
        ?string $profilePicture,
        string $role = 'provider',
        array $socialLinks = [] // Paramètre optionnel pour les liens sociaux
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->password = $password;
        $this->title = $title;
        $this->presentation = $presentation;
        $this->country = $country;
        $this->profilePicture = $profilePicture;
        $this->role = $role;
        $this->createdAt = new DateTimeImmutable();
        $this->socialLinks = $socialLinks; // Initialisation des liens sociaux
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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getPresentation(): string
    {
        return $this->presentation;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    /**
     * @return AvailabilitySlot[]
     */
    public function getAvailabilitySlots(): array
    {
        return $this->availabilitySlots;
    }

    /**
     * @return Skill[]
     */
    public function getSkills(): array
    {
        return $this->skills;
    }

    /**
     * @return string[]
     */
    public function getSocialLinks(): array
    {
        return $this->socialLinks; // Nouveau getter
    }

    // Setters

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function setLastName(string $lastName): void
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

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setPresentation(string $presentation): void
    {
        $this->presentation = $presentation;
    }

    public function setProfilePicture(?string $profilePicture): void
    {
        $this->profilePicture = $profilePicture;
    }

    public function setAvailabilitySlots(array $slots): void
    {
        $this->availabilitySlots = $slots;
    }

    public function addAvailabilitySlot(AvailabilitySlot $slot): void
    {
        $this->availabilitySlots[] = $slot;
    }

    public function setSkills(array $skills): void
    {
        $this->skills = $skills;
    }

    public function setSocialLinks(array $socialLinks): void
    {
        $this->socialLinks = $socialLinks; // Nouveau setter
    }
}
