<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Repository;

use Soosuuke\IaPlatform\Entity\Education;
use Soosuuke\IaPlatform\Config\Database;
use ReflectionClass;

class EducationRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function findById(int $id): ?Education
    {
        $stmt = $this->pdo->prepare('SELECT * FROM education WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        return $data ? $this->mapToEducation($data) : null;
    }

    public function findByProviderId(int $providerId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM education WHERE provider_id = ? ORDER BY started_at DESC');
        $stmt->execute([$providerId]);

        $educations = [];
        while ($row = $stmt->fetch()) {
            $educations[] = $this->mapToEducation($row);
        }

        return $educations;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM education ORDER BY started_at DESC');
        $educations = [];

        while ($row = $stmt->fetch()) {
            $educations[] = $this->mapToEducation($row);
        }

        return $educations;
    }

    public function save(Education $education): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO education (provider_id, title, institution_name, description, started_at, ended_at, institution_image)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $education->getProviderId(),
            $education->getTitle(),
            $education->getInstitutionName(),
            $education->getDescription(),
            $education->getStartedAt()->format('Y-m-d'),
            $education->getEndedAt()?->format('Y-m-d'),
            $education->getInstitutionImage()
        ]);

        $id = (int) $this->pdo->lastInsertId();
        $ref = new ReflectionClass(Education::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($education, $id);
    }

    public function update(Education $education): void
    {
        $stmt = $this->pdo->prepare('
            UPDATE education SET
                provider_id = ?, title = ?, institution_name = ?, description = ?,
                started_at = ?, ended_at = ?, institution_image = ?
            WHERE id = ?
        ');
        $stmt->execute([
            $education->getProviderId(),
            $education->getTitle(),
            $education->getInstitutionName(),
            $education->getDescription(),
            $education->getStartedAt()->format('Y-m-d'),
            $education->getEndedAt()?->format('Y-m-d'),
            $education->getInstitutionImage(),
            $education->getId()
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM education WHERE id = ?');
        $stmt->execute([$id]);
    }

    private function mapToEducation(array $data): Education
    {
        $education = new Education(
            (int) $data['provider_id'],
            $data['title'],
            $data['institution_name'],
            $data['description'],
            new \DateTimeImmutable($data['started_at']),
            $data['ended_at'] ? new \DateTimeImmutable($data['ended_at']) : null,
            $data['institution_image'] ?? null
        );

        $ref = new ReflectionClass(Education::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($education, (int) $data['id']);

        return $education;
    }
}
