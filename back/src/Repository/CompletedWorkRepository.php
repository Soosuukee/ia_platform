<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Repository;

use Soosuuke\IaPlatform\Config\Database;
use Soosuuke\IaPlatform\Entity\CompletedWork;
use DateTimeImmutable;
use ReflectionClass;

class CompletedWorkRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function findById(int $id): ?CompletedWork
    {
        $stmt = $this->pdo->prepare('SELECT * FROM completed_work WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        return $data ? $this->hydrate($data) : null;
    }

    public function findAllByProviderId(int $providerId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM completed_work WHERE provider_id = ? ORDER BY start_date DESC');
        $stmt->execute([$providerId]);

        $works = [];
        while ($row = $stmt->fetch()) {
            $works[] = $this->hydrate($row);
        }

        return $works;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM completed_work ORDER BY start_date DESC');
        $works = [];

        while ($row = $stmt->fetch()) {
            $works[] = $this->hydrate($row);
        }

        return $works;
    }

    public function save(CompletedWork $work): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO completed_work (provider_id, company, title, description, start_date, end_date)
            VALUES (?, ?, ?, ?, ?, ?)
        ');

        $stmt->execute([
            $work->getProviderId(),
            $work->getCompany(),
            $work->getTitle(),
            $work->getDescription(),
            $work->getStartDate()->format('Y-m-d'),
            $work->getEndDate()?->format('Y-m-d')
        ]);

        $id = (int) $this->pdo->lastInsertId();
        $ref = new ReflectionClass(CompletedWork::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($work, $id);
    }

    public function update(CompletedWork $work): void
    {
        $stmt = $this->pdo->prepare('
            UPDATE completed_work
            SET company = ?, title = ?, description = ?, start_date = ?, end_date = ?
            WHERE id = ?
        ');

        $stmt->execute([
            $work->getCompany(),
            $work->getTitle(),
            $work->getDescription(),
            $work->getStartDate()->format('Y-m-d H:i:s'),
            $work->getEndDate()?->format('Y-m-d H:i:s'),
            $work->getId(),
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM completed_work WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function deleteByProviderId(int $providerId): void
    {
        // Assuming a PDO connection or similar
        $stmt = $this->pdo->prepare("DELETE FROM completed_work WHERE provider_id = ?");
        $stmt->execute([$providerId]);
    }

    private function hydrate(array $data): CompletedWork
    {
        $work = new CompletedWork(
            (int) $data['provider_id'],
            $data['company'],
            $data['title'],
            $data['description'],
            new DateTimeImmutable($data['start_date']),
            $data['end_date'] ? new DateTimeImmutable($data['end_date']) : null
        );

        $ref = new ReflectionClass(CompletedWork::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($work, (int) $data['id']);

        return $work;
    }
}
