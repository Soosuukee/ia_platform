<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Repository;

use Soosuuke\IaPlatform\Config\Database;
use Soosuuke\IaPlatform\Entity\Skill;
use ReflectionClass;

class SkillRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function findById(int $id): ?Skill
    {
        $stmt = $this->pdo->prepare('SELECT * FROM skill WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        return $data ? $this->mapToSkill($data) : null;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM skill ORDER BY name ASC');
        $skills = [];

        while ($row = $stmt->fetch()) {
            $skills[] = $this->mapToSkill($row);
        }

        return $skills;
    }

    public function save(Skill $skill): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO skill (name) VALUES (?)');
        $stmt->execute([$skill->getName()]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM skill WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function findByName(string $name): ?Skill
    {
        $stmt = $this->pdo->prepare('SELECT * FROM skill WHERE name = ? LIMIT 1');
        $stmt->execute([$name]);
        $data = $stmt->fetch();

        return $data ? $this->mapToSkill($data) : null;
    }

    public function mapToSkill(array $data): Skill
    {
        $skill = new Skill($data['name']);

        $ref = new ReflectionClass(Skill::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($skill, (int) $data['id']);

        return $skill;
    }
}
