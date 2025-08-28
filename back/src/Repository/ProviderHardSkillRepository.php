<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Repository;

use Soosuuke\IaPlatform\Config\Database;

class ProviderHardSkillRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function findByProviderId(int $providerId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT hs.* FROM hard_skill hs
            INNER JOIN provider_hard_skills phs ON phs.hard_skill_id = hs.id
            WHERE phs.provider_id = ?
            ORDER BY hs.title
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
        $stmt = $this->pdo->prepare('INSERT INTO provider_hard_skills (provider_id, hard_skill_id) VALUES (?, ?)');
        $stmt->execute([$providerId, $skillId]);
    }

    public function removeSkillFromProvider(int $providerId, int $skillId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM provider_hard_skills WHERE provider_id = ? AND hard_skill_id = ?');
        $stmt->execute([$providerId, $skillId]);
    }

    public function removeAllSkillsFromProvider(int $providerId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM provider_hard_skills WHERE provider_id = ?');
        $stmt->execute([$providerId]);
    }
}
