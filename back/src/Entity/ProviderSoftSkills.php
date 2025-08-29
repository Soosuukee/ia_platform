<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

class ProviderSoftSkills
{
    private int $id;
    private int $providerId;
    private int $softSkillId;

    public function __construct(int $providerId, int $softSkillId)
    {
        $this->providerId = $providerId;
        $this->softSkillId = $softSkillId;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getProviderId(): int
    {
        return $this->providerId;
    }

    public function getSoftSkillId(): int
    {
        return $this->softSkillId;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'providerId' => $this->providerId,
        ];
    }
}
