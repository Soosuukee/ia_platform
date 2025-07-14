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
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        return $data ? $this->mapToClient($data) : null;
    }

    public function findByEmail(string $email): ?Client
    {
        $stmt = $this->pdo->prepare('SELECT * FROM client WHERE email = ?');
        $stmt->execute([$email]);
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

        $stmt->execute([
            $client->getFirstName(),
            $client->getLastName(),
            $client->getEmail(),
            $client->getPassword(),
            $client->getRole(),
            $client->getCountry(),
            $client->getCreatedAt()->format('Y-m-d H:i:s'),
        ]);

        $ref = new ReflectionClass(Client::class);
        $prop = $ref->getProperty('id');
        $prop->setAccessible(true);
        $prop->setValue($client, (int) $this->pdo->lastInsertId());
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM client WHERE id = ?');
        $stmt->execute([$id]);
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
