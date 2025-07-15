<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Repository;

use Soosuuke\IaPlatform\Config\Database;
use Soosuuke\IaPlatform\Entity\Request;
use DateTimeImmutable;
use ReflectionClass;

class RequestRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function findById(int $id): ?Request
    {
        $stmt = $this->pdo->prepare('SELECT * FROM request WHERE request_id = ?');
        if (!$stmt->execute([$id])) {
            throw new \RuntimeException("Failed to fetch request with ID $id");
        }
        $data = $stmt->fetch();

        return $data ? $this->mapToRequest($data) : null;
    }

    public function findAllByProviderId(int $providerId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM request WHERE provider_id = ? ORDER BY created_at DESC');
        if (!$stmt->execute([$providerId])) {
            throw new \RuntimeException("Failed to fetch requests for provider ID $providerId");
        }

        $requests = [];
        while ($row = $stmt->fetch()) {
            $requests[] = $this->mapToRequest($row);
        }

        return $requests;
    }

    public function findAllByClientId(int $clientId, int $limit = 10): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM request WHERE client_id = ? ORDER BY created_at DESC LIMIT ?');
        if (!$stmt->execute([$clientId, $limit])) {
            throw new \RuntimeException("Failed to fetch requests for client ID $clientId");
        }

        $requests = [];
        while ($row = $stmt->fetch()) {
            $requests[] = $this->mapToRequest($row);
        }

        return $requests;
    }

    public function save(Request $request): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO request (client_id, provider_id, title, description, created_at, status)
            VALUES (?, ?, ?, ?, ?, ?)
        ');
        if (!$stmt->execute([
            $request->getClientId(),
            $request->getProviderId(),
            $request->getTitle(),
            $request->getDescription(),
            $request->getCreatedAt()->format('Y-m-d H:i:s'),
            $request->getStatus()
        ])) {
            throw new \RuntimeException('Failed to save request');
        }

        $id = (int) $this->pdo->lastInsertId();
        $ref = new ReflectionClass(Request::class);
        $idProp = $ref->getProperty('requestId');
        $idProp->setAccessible(true);
        $idProp->setValue($request, $id);
    }

    public function updateStatus(int $requestId, string $newStatus): void
    {
        $stmt = $this->pdo->prepare('UPDATE request SET status = ? WHERE request_id = ?');
        if (!$stmt->execute([$newStatus, $requestId])) {
            throw new \RuntimeException("Failed to update status for request ID $requestId");
        }
    }

    public function delete(int $requestId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM request WHERE request_id = ?');
        if (!$stmt->execute([$requestId])) {
            throw new \RuntimeException("Failed to delete request with ID $requestId");
        }
    }

    public function deleteByClientId(int $clientId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM request WHERE client_id = ?');
        if (!$stmt->execute([$clientId])) {
            throw new \RuntimeException("Failed to delete requests for client ID $clientId");
        }
    }

    public function deleteByProviderId(int $providerId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM request WHERE provider_id = ?');
        if (!$stmt->execute([$providerId])) {
            throw new \RuntimeException("Failed to delete requests for provider ID $providerId");
        }
    }

    private function mapToRequest(array $data): Request
    {
        $request = new Request(
            (int) $data['client_id'],
            (int) $data['provider_id'],
            $data['title'],
            $data['description']
        );

        $ref = new ReflectionClass(Request::class);

        // ID
        $idProp = $ref->getProperty('requestId');
        $idProp->setAccessible(true);
        $idProp->setValue($request, (int) $data['request_id']);

        // createdAt
        $createdAtProp = $ref->getProperty('createdAt');
        $createdAtProp->setAccessible(true);
        $createdAtProp->setValue($request, new DateTimeImmutable($data['created_at']));

        // status
        $statusProp = $ref->getProperty('status');
        $statusProp->setAccessible(true);
        $statusProp->setValue($request, $data['status']);

        return $request;
    }
}
