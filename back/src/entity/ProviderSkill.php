<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

class ProviderSkill

{
    private int $providerId;
    private int $skillId;

    public function __construct(int $providerId, int $skillId)
    {
        $this->providerId = $providerId;
        $this->skillId = $skillId;
    }

    // Getters
    public function getProviderId(): int
    {
        return $this->providerId;
    }

    public function getSkillId(): int
    {
        return $this->skillId;
    }
}
