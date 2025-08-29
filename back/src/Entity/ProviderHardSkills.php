<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

class ProviderHardSkills
{
    private int $id;
    private int $providerId;
    private int $hardSkillId;

    public function __construct(int $providerId, int $hardSkillId)
    {
        $this->providerId = $providerId;
        $this->hardSkillId = $hardSkillId;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getProviderId(): int
    {
        return $this->providerId;
    }

    public function getHardSkillId(): int
    {
        return $this->hardSkillId;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'providerId' => $this->providerId,
        ];
    }
}
