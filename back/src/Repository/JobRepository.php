<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Repository;

use Soosuuke\IaPlatform\Entity\Job;
use Soosuuke\IaPlatform\Config\Database;
use ReflectionClass;

class JobRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function findById(int $id): ?Job
    {
        $stmt = $this->pdo->prepare('SELECT * FROM job WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        return $data ? $this->mapToJob($data) : null;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM job ORDER BY title');
        $jobs = [];

        while ($row = $stmt->fetch()) {
            $jobs[] = $this->mapToJob($row);
        }

        return $jobs;
    }

    public function findBySlug(string $slug): ?Job
    {
        $stmt = $this->pdo->prepare('SELECT * FROM job WHERE slug = ?');
        $stmt->execute([$slug]);
        $data = $stmt->fetch();

        return $data ? $this->mapToJob($data) : null;
    }

    public function save(Job $job): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO job (title, slug) VALUES (?, ?)');
        $stmt->execute([$job->getTitle(), $job->getSlug()]);

        $id = (int) $this->pdo->lastInsertId();
        $ref = new ReflectionClass(Job::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($job, $id);
    }

    public function update(Job $job): void
    {
        $stmt = $this->pdo->prepare('UPDATE job SET title = ?, slug = ? WHERE id = ?');
        $stmt->execute([$job->getTitle(), $job->getSlug(), $job->getId()]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM job WHERE id = ?');
        $stmt->execute([$id]);
    }

    private function mapToJob(array $data): Job
    {
        $job = new Job($data['title'], $data['slug']);

        $ref = new ReflectionClass(Job::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($job, (int) $data['id']);

        return $job;
    }
}
