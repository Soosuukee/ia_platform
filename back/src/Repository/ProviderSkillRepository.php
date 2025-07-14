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

    public function addSkillToProvider(ProviderSkill $link): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO provider_skill (provider_id, skill_id) VALUES (?, ?)');
        $stmt->execute([
            $link->getProviderId(),
            $link->getSkillId()
        ]);
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
            $skills[] = $skillRepo->mapToSkill($row); // suppose que SkillRepository::mapToSkill existe
        }

        return $skills;
    }

    public function deleteAllSkillsForProvider(int $providerId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM provider_skill WHERE provider_id = ?');
        $stmt->execute([$providerId]);
    }
}
