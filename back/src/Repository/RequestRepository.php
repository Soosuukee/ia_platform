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
        $stmt->execute([$id]);

        $data = $stmt->fetch();
        return $data ? $this->mapToRequest($data) : null;
    }

    public function findAllByProviderId(int $providerId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM request WHERE provider_id = ? ORDER BY created_at DESC');
        $stmt->execute([$providerId]);

        $requests = [];
        while ($row = $stmt->fetch()) {
            $requests[] = $this->mapToRequest($row);
        }

        return $requests;
    }

    public function findAllByClientId(int $clientId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM request WHERE client_id = ? ORDER BY created_at DESC');
        $stmt->execute([$clientId]);

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

        $stmt->execute([
            $request->getClientId(),
            $request->getProvider(),
            $request->getTitle(),
            $request->getDescription(),
            $request->getCreatedAt()->format('Y-m-d H:i:s'),
            $request->getStatus()
        ]);

        $id = (int) $this->pdo->lastInsertId();
        $ref = new ReflectionClass(Request::class);
        $idProp = $ref->getProperty('requestId');
        $idProp->setAccessible(true);
        $idProp->setValue($request, $id);
    }

    public function updateStatus(int $requestId, string $newStatus): void
    {
        $stmt = $this->pdo->prepare('UPDATE request SET status = ? WHERE request_id = ?');
        $stmt->execute([$newStatus, $requestId]);
    }

    public function delete(int $requestId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM request WHERE request_id = ?');
        $stmt->execute([$requestId]);
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
