<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

class ProviderLanguage
{
    private int $id;
    private int $providerId;
    private int $languageId;

    public function __construct(int $providerId, int $languageId)
    {
        $this->providerId = $providerId;
        $this->languageId = $languageId;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getProviderId(): int
    {
        return $this->providerId;
    }

    public function getLanguageId(): int
    {
        return $this->languageId;
    }
}
