<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Repository;

use Soosuuke\IaPlatform\Config\Database;

class ProviderSoftSkillRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function findByProviderId(int $providerId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT ss.* FROM soft_skill ss
            INNER JOIN provider_soft_skills pss ON pss.soft_skill_id = ss.id
            WHERE pss.provider_id = ?
            ORDER BY ss.title
        ');
        $stmt->execute([$providerId]);

        $skills = [];
        while ($row = $stmt->fetch()) {
            $skills[] = $row;
        }

        return $skills;
    }

    public function addSkillToProvider(int $providerId, int $skillId): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO provider_soft_skills (provider_id, soft_skill_id) VALUES (?, ?)');
        $stmt->execute([$providerId, $skillId]);
    }

    public function removeSkillFromProvider(int $providerId, int $skillId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM provider_soft_skills WHERE provider_id = ? AND soft_skill_id = ?');
        $stmt->execute([$providerId, $skillId]);
    }

    public function removeAllSkillsFromProvider(int $providerId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM provider_soft_skills WHERE provider_id = ?');
        $stmt->execute([$providerId]);
    }
}
