<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

use Soosuuke\IaPlatform\Entity\Provider;
use Soosuuke\IaPlatform\Entity\Skill;

class ProviderSkill
{
    private Provider $provider;
    private Skill $skill;

    public function __construct(Provider $provider, Skill $skill)
    {
        $this->provider = $provider;
        $this->skill = $skill;
    }

    // Getters
    public function getProvider(): Provider
    {
        return $this->provider;
    }

    public function getSkill(): Skill
    {
        return $this->skill;
    }
}
