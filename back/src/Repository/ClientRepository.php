<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Repository;

use Soosuuke\IaPlatform\Entity\Client;
use Soosuuke\IaPlatform\Config\Database;
use DateTimeImmutable;
use ReflectionClass;

class ClientRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function findById(int $id): ?Client
    {
        $stmt = $this->pdo->prepare('SELECT * FROM client WHERE id = ?');
        if (!$stmt->execute([$id])) {
            throw new \RuntimeException("Failed to fetch client with ID $id");
        }
        $data = $stmt->fetch();

        return $data ? $this->mapToClient($data) : null;
    }

    public function findByEmail(string $email): ?Client
    {
        $stmt = $this->pdo->prepare('SELECT * FROM client WHERE email = ?');
        if (!$stmt->execute([$email])) {
            throw new \RuntimeException("Failed to fetch client with email $email");
        }
        $data = $stmt->fetch();

        return $data ? $this->mapToClient($data) : null;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM client');
        $clients = [];

        while ($row = $stmt->fetch()) {
            $clients[] = $this->mapToClient($row);
        }

        return $clients;
    }

    public function save(Client $client): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO client (first_name, last_name, email, password, role, country, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ');

        if (!$stmt->execute([
            $client->getFirstName(),
            $client->getLastName(),
            $client->getEmail(),
            $client->getPassword(),
            $client->getRole(),
            $client->getCountry(),
            $client->getCreatedAt()->format('Y-m-d H:i:s'),
        ])) {
            throw new \RuntimeException('Failed to save client');
        }

        $ref = new ReflectionClass(Client::class);
        $prop = $ref->getProperty('id');
        $prop->setAccessible(true);
        $prop->setValue($client, (int) $this->pdo->lastInsertId());
    }

    public function update(Client $client): void
    {
        $stmt = $this->pdo->prepare('
            UPDATE client
            SET first_name = ?, last_name = ?, email = ?, password = ?, country = ?
            WHERE id = ?
        ');

        if (!$stmt->execute([
            $client->getFirstName(),
            $client->getLastName(),
            $client->getEmail(),
            $client->getPassword(),
            $client->getCountry(),
            $client->getId(),
        ])) {
            throw new \RuntimeException("Failed to update client with ID {$client->getId()}");
        }
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM client WHERE id = ?');
        if (!$stmt->execute([$id])) {
            throw new \RuntimeException("Failed to delete client with ID $id");
        }
    }

    public function deleteByClientId(int $clientId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM client WHERE id = ?');
        if (!$stmt->execute([$clientId])) {
            throw new \RuntimeException("Failed to delete client with ID $clientId");
        }
    }

    private function mapToClient(array $data): Client
    {
        $client = new Client(
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['password'],
            $data['country']
        );

        $ref = new ReflectionClass(Client::class);

        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($client, (int) $data['id']);

        $createdAtProp = $ref->getProperty('createdAt');
        $createdAtProp->setAccessible(true);
        $createdAtProp->setValue($client, new DateTimeImmutable($data['created_at']));

        return $client;
    }
}
