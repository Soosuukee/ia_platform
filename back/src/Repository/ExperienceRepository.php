<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Repository;

use Soosuuke\IaPlatform\Entity\Experience;
use Soosuuke\IaPlatform\Config\Database;
use ReflectionClass;

class ExperienceRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function findById(int $id): ?Experience
    {
        $stmt = $this->pdo->prepare('SELECT * FROM experience WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        return $data ? $this->mapToExperience($data) : null;
    }

    public function findByProviderId(int $providerId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM experience WHERE provider_id = ? ORDER BY started_at DESC');
        $stmt->execute([$providerId]);

        $experiences = [];
        while ($row = $stmt->fetch()) {
            $experiences[] = $this->mapToExperience($row);
        }

        return $experiences;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM experience ORDER BY started_at DESC');
        $experiences = [];

        while ($row = $stmt->fetch()) {
            $experiences[] = $this->mapToExperience($row);
        }

        return $experiences;
    }

    public function save(Experience $experience): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO experience (provider_id, title, company_name, first_task, second_task, third_task, started_at, ended_at, company_logo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $experience->getProviderId(),
            $experience->getTitle(),
            $experience->getCompanyName(),
            $experience->getFirstTask(),
            $experience->getSecondTask(),
            $experience->getThirdTask(),
            $experience->getStartedAt()->format('Y-m-d'),
            $experience->getEndedAt()?->format('Y-m-d'),
            $experience->getCompanyLogo()
        ]);

        $id = (int) $this->pdo->lastInsertId();
        $ref = new ReflectionClass(Experience::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($experience, $id);
    }

    public function update(Experience $experience): void
    {
        $stmt = $this->pdo->prepare('
            UPDATE experience SET
                provider_id = ?, title = ?, company_name = ?, first_task = ?, second_task = ?, third_task = ?,
                started_at = ?, ended_at = ?, company_logo = ?
            WHERE id = ?
        ');
        $stmt->execute([
            $experience->getProviderId(),
            $experience->getTitle(),
            $experience->getCompanyName(),
            $experience->getFirstTask(),
            $experience->getSecondTask(),
            $experience->getThirdTask(),
            $experience->getStartedAt()->format('Y-m-d'),
            $experience->getEndedAt()?->format('Y-m-d'),
            $experience->getCompanyLogo(),
            $experience->getId()
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM experience WHERE id = ?');
        $stmt->execute([$id]);
    }

    private function mapToExperience(array $data): Experience
    {
        $experience = new Experience(
            (int) $data['provider_id'],
            $data['title'],
            $data['company_name'],
            $data['first_task'],
            new \DateTimeImmutable($data['started_at']),
            $data['ended_at'] ? new \DateTimeImmutable($data['ended_at']) : null,
            $data['second_task'] ?? null,
            $data['third_task'] ?? null,
            $data['company_logo'] ?? null
        );

        $ref = new ReflectionClass(Experience::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($experience, (int) $data['id']);

        return $experience;
    }
}
