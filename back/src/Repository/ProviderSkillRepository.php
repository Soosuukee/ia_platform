<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Repository;

use Soosuuke\IaPlatform\Config\Database;
use Soosuuke\IaPlatform\Entity\ProviderSkill;

class ProviderSkillRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function save(ProviderSkill $providerSkill): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO provider_skill (provider_id, skill_id)
            VALUES (?, ?)
        ');
        if (!$stmt->execute([
            $providerSkill->getProviderId(),
            $providerSkill->getSkillId(),
        ])) {
            throw new \RuntimeException('Failed to save provider skill for provider ID ' . $providerSkill->getProviderId());
        }
    }

    public function findAllSkillsByProviderId(int $providerId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT s.* FROM skill s
            INNER JOIN provider_skill ps ON ps.skill_id = s.id
            WHERE ps.provider_id = ?
        ');
        $stmt->execute([$providerId]);

        $skills = [];
        $skillRepo = new SkillRepository();

        while ($row = $stmt->fetch()) {
            $skills[] = $skillRepo->mapToSkill($row);
        }

        return $skills;
    }

    public function deleteByProviderId(int $providerId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM provider_skill WHERE provider_id = ?');
        if (!$stmt->execute([$providerId])) {
            throw new \RuntimeException('Failed to delete provider skills for provider ID ' . $providerId);
        }
    }

    public function deleteByProviderAndSkillId(int $providerId, int $skillId): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM provider_skill WHERE provider_id = ? AND skill_id = ?');
        if (!$stmt->execute([$providerId, $skillId])) {
            throw new \RuntimeException("Failed to delete skill ID $skillId for provider ID $providerId");
        }
        return $stmt->rowCount() > 0;
    }
}
