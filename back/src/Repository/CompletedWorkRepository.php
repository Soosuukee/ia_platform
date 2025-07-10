<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Repository;

use Soosuuke\IaPlatform\Config\Database;
use Soosuuke\IaPlatform\Entity\CompletedWork;
use Soosuuke\IaPlatform\Repository\ProviderRepository;
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

        return $data ? $this->mapToCompletedWork($data) : null;
    }

    public function findAllByProviderId(int $providerId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM completed_work WHERE provider_id = ? ORDER BY completed_at DESC');
        $stmt->execute([$providerId]);

        $works = [];
        while ($row = $stmt->fetch()) {
            $works[] = $this->mapToCompletedWork($row);
        }

        return $works;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM completed_work ORDER BY completed_at DESC');
        $works = [];

        while ($row = $stmt->fetch()) {
            $works[] = $this->mapToCompletedWork($row);
        }

        return $works;
    }

    public function save(CompletedWork $work): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO completed_work (provider_id, title, description, completed_at)
            VALUES (?, ?, ?, ?)
        ');

        $stmt->execute([
            $work->getProvider()->getId(),
            $work->getTitle(),
            $work->getDescription(),
            $work->getCompletedAt()->format('Y-m-d H:i:s')
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM completed_work WHERE id = ?');
        $stmt->execute([$id]);
    }

    private function mapToCompletedWork(array $data): CompletedWork
    {
        $providerRepo = new ProviderRepository();
        $provider = $providerRepo->findById((int) $data['provider_id']);

        $work = new CompletedWork(
            $provider,
            $data['title'],
            $data['description']
        );

        $ref = new ReflectionClass(CompletedWork::class);

        // Inject ID
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($work, (int) $data['id']);

        // Inject completedAt
        $completedAtProp = $ref->getProperty('completedAt');
        $completedAtProp->setAccessible(true);
        $completedAtProp->setValue($work, new DateTimeImmutable($data['completed_at']));

        return $work;
    }
}
