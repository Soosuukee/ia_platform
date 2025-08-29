<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

class SocialLink
{
    private int $id = 0;
    private int $providerId;
    private string $platform;
    private string $url;

    public function __construct(
        int $providerId,
        string $platform,
        string $url
    ) {
        $this->validateInputs($platform, $url);

        $this->providerId = $providerId;
        $this->platform = trim($platform);
        $this->url = trim($url);
    }

    private function validateInputs(string $platform, string $url): void
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
    }

    // Getters
    public function getId(): int
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

    // Setters
    public function setProviderId(int $providerId): void
    {
        $this->providerId = $providerId;
    }

    public function setPlatform(string $platform): void
    {
        $this->platform = trim($platform);
    }

    public function setUrl(string $url): void
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('L\'URL n\'est pas valide');
        }
        $this->url = trim($url);
    }

    // Méthodes pour l'API
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'providerId' => $this->providerId,
            'platform' => $this->platform,
            'url' => $this->url
        ];
    }
}
