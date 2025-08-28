<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

use DateTimeImmutable;

class SocialLink
{
    private int $id;
    private int $providerId;
    private string $platform;
    private string $url;
    private string $username;
    private bool $isActive;
    private DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt;

    public function __construct(
        int $providerId,
        string $platform,
        string $url,
        string $username,
        bool $isActive = true
    ) {
        $this->validateInputs($platform, $url, $username);

        $this->providerId = $providerId;
        $this->platform = trim($platform);
        $this->url = trim($url);
        $this->username = trim($username);
        $this->isActive = $isActive;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = null;
    }

    private function validateInputs(string $platform, string $url, string $username): void
    {
        if (empty(trim($platform))) {
            throw new \InvalidArgumentException('La plateforme ne peut pas être vide');
        }
        if (empty(trim($url))) {
            throw new \InvalidArgumentException('L\'URL ne peut pas être vide');
        }
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('L\'URL n\'est pas valide');
        }
        if (empty(trim($username))) {
            throw new \InvalidArgumentException('Le nom d\'utilisateur ne peut pas être vide');
        }
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getSocialLinkId(): int
    {
        return $this->id;
    }

    public function getProviderId(): int
    {
        return $this->providerId;
    }

    public function getPlatform(): string
    {
        return $this->platform;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    // Setters
    public function setProviderId(int $providerId): void
    {
        $this->providerId = $providerId;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function setPlatform(string $platform): void
    {
        $this->platform = trim($platform);
        $this->updatedAt = new DateTimeImmutable();
    }

    public function setUrl(string $url): void
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('L\'URL n\'est pas valide');
        }
        $this->url = trim($url);
        $this->updatedAt = new DateTimeImmutable();
    }

    public function setUsername(string $username): void
    {
        $this->username = trim($username);
        $this->updatedAt = new DateTimeImmutable();
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
        $this->updatedAt = new DateTimeImmutable();
    }

    // Méthodes pour l'API
    public function toArray(): array
    {
        return [
            'socialLinkId' => $this->id,
            'providerId' => $this->providerId,
            'platform' => $this->platform,
            'url' => $this->url,
            'username' => $this->username,
            'isActive' => $this->isActive,
            'createdAt' => $this->createdAt->format('Y-m-d\TH:i:s.v\Z'),
            'updatedAt' => $this->updatedAt?->format('Y-m-d\TH:i:s.v\Z')
        ];
    }
}
