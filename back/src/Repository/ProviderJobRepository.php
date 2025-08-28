<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Repository;

use Soosuuke\IaPlatform\Config\Database;

class ProviderJobRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function findByProviderId(int $providerId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT j.* FROM job j
            INNER JOIN provider_job pj ON pj.job_id = j.id
            WHERE pj.provider_id = ?
            ORDER BY j.title
        ');
        $stmt->execute([$providerId]);

        $jobs = [];
        while ($row = $stmt->fetch()) {
            $jobs[] = $row;
        }

        return $jobs;
    }

    public function addJobToProvider(int $providerId, int $jobId): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO provider_job (provider_id, job_id) VALUES (?, ?)');
        $stmt->execute([$providerId, $jobId]);
    }

    public function removeJobFromProvider(int $providerId, int $jobId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM provider_job WHERE provider_id = ? AND job_id = ?');
        $stmt->execute([$providerId, $jobId]);
    }

    public function removeAllJobsFromProvider(int $providerId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM provider_job WHERE provider_id = ?');
        $stmt->execute([$providerId]);
    }
}
