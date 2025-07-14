<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Repository;

use Soosuuke\IaPlatform\Entity\ProvidedService;
use Soosuuke\IaPlatform\Config\Database;
use ReflectionClass;

class ProvidedServiceRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function findById(int $id): ?ProvidedService
    {
        $stmt = $this->pdo->prepare('SELECT * FROM provided_service WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        return $data ? $this->mapToProvidedService($data) : null;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM provided_service');
        $services = [];

        while ($row = $stmt->fetch()) {
            $services[] = $this->mapToProvidedService($row);
        }

        return $services;
    }

    public function findByProviderId(int $providerId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM provided_service WHERE provider_id = ?');
        $stmt->execute([$providerId]);

        $services = [];
        while ($row = $stmt->fetch()) {
            $services[] = $this->mapToProvidedService($row);
        }

        return $services;
    }

    public function save(ProvidedService $service): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO provided_service (title, description, min_price, max_price, duration, provider_id)
            VALUES (?, ?, ?, ?, ?, ?)
        ');

        $stmt->execute([
            $service->getTitle(),
            $service->getDescription(),
            $service->getMinPrice(),
            $service->getMaxPrice(),
            $service->getDuration(),
            $service->getProviderId()
        ]);

        $id = (int) $this->pdo->lastInsertId();
        $ref = new ReflectionClass(ProvidedService::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($service, $id);
    }

    public function update(ProvidedService $service): void
    {
        $stmt = $this->pdo->prepare('
            UPDATE provided_service
            SET title = ?, description = ?, min_price = ?, max_price = ?, duration = ?
            WHERE id = ?
        ');

        $stmt->execute([
            $service->getTitle(),
            $service->getDescription(),
            $service->getMinPrice(),
            $service->getMaxPrice(),
            $service->getDuration(),
            $service->getId()
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM provided_service WHERE id = ?');
        $stmt->execute([$id]);
    }

    private function mapToProvidedService(array $data): ProvidedService
    {
        $service = new ProvidedService(
            $data['title'],
            $data['description'],
            $data['min_price'] !== null ? (int)$data['min_price'] : null,
            $data['max_price'] !== null ? (int)$data['max_price'] : null,
            $data['duration'],
            (int)$data['provider_id']
        );

        $ref = new ReflectionClass(ProvidedService::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($service, (int) $data['id']);

        return $service;
    }
}
