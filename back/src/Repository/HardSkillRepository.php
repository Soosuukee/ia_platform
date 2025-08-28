<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Repository;

use Soosuuke\IaPlatform\Entity\HardSkill;
use Soosuuke\IaPlatform\Config\Database;
use ReflectionClass;

class HardSkillRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function findById(int $id): ?HardSkill
    {
        $stmt = $this->pdo->prepare('SELECT * FROM hard_skill WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        return $data ? $this->mapToHardSkill($data) : null;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM hard_skill ORDER BY title');
        $skills = [];

        while ($row = $stmt->fetch()) {
            $skills[] = $this->mapToHardSkill($row);
        }

        return $skills;
    }

    public function save(HardSkill $skill): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO hard_skill (title) VALUES (?)');
        $stmt->execute([$skill->getTitle()]);

        $id = (int) $this->pdo->lastInsertId();
        $ref = new ReflectionClass(HardSkill::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($skill, $id);
    }

    public function update(HardSkill $skill): void
    {
        $stmt = $this->pdo->prepare('UPDATE hard_skill SET title = ? WHERE id = ?');
        $stmt->execute([$skill->getTitle(), $skill->getId()]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM hard_skill WHERE id = ?');
        $stmt->execute([$id]);
    }

    private function mapToHardSkill(array $data): HardSkill
    {
        $skill = new HardSkill($data['title']);

        $ref = new ReflectionClass(HardSkill::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($skill, (int) $data['id']);

        return $skill;
    }
}
