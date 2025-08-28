<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Repository;

use Soosuuke\IaPlatform\Entity\SoftSkill;
use Soosuuke\IaPlatform\Config\Database;
use ReflectionClass;

class SoftSkillRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function findById(int $id): ?SoftSkill
    {
        $stmt = $this->pdo->prepare('SELECT * FROM soft_skill WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        return $data ? $this->mapToSoftSkill($data) : null;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM soft_skill ORDER BY title');
        $skills = [];

        while ($row = $stmt->fetch()) {
            $skills[] = $this->mapToSoftSkill($row);
        }

        return $skills;
    }

    public function save(SoftSkill $skill): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO soft_skill (title) VALUES (?)');
        $stmt->execute([$skill->getTitle()]);

        $id = (int) $this->pdo->lastInsertId();
        $ref = new ReflectionClass(SoftSkill::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($skill, $id);
    }

    public function update(SoftSkill $skill): void
    {
        $stmt = $this->pdo->prepare('UPDATE soft_skill SET title = ? WHERE id = ?');
        $stmt->execute([$skill->getTitle(), $skill->getId()]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM soft_skill WHERE id = ?');
        $stmt->execute([$id]);
    }

    private function mapToSoftSkill(array $data): SoftSkill
    {
        $skill = new SoftSkill($data['title']);

        $ref = new ReflectionClass(SoftSkill::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($skill, (int) $data['id']);

        return $skill;
    }
}
