<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Repository;

use Soosuuke\IaPlatform\Entity\Location;
use Soosuuke\IaPlatform\Config\Database;
use ReflectionClass;

class LocationRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function findById(int $id): ?Location
    {
        $stmt = $this->pdo->prepare('SELECT * FROM location WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        return $data ? $this->mapToLocation($data) : null;
    }

    public function findByAccountHolder(int $accountHolderId, string $accountHolderType): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM location WHERE account_holder_id = ? AND account_holder_type = ? ORDER BY id');
        $stmt->execute([$accountHolderId, $accountHolderType]);

        $locations = [];
        while ($row = $stmt->fetch()) {
            $locations[] = $this->mapToLocation($row);
        }

        return $locations;
    }

    public function findByCity(string $city): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM location WHERE city = ? ORDER BY id');
        $stmt->execute([$city]);

        $locations = [];
        while ($row = $stmt->fetch()) {
            $locations[] = $this->mapToLocation($row);
        }

        return $locations;
    }

    public function findByState(string $state): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM location WHERE state = ? ORDER BY id');
        $stmt->execute([$state]);

        $locations = [];
        while ($row = $stmt->fetch()) {
            $locations[] = $this->mapToLocation($row);
        }

        return $locations;
    }

    public function findProvidersByCity(string $city): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM location WHERE city = ? AND account_holder_type = "provider" ORDER BY id');
        $stmt->execute([$city]);

        $locations = [];
        while ($row = $stmt->fetch()) {
            $locations[] = $this->mapToLocation($row);
        }

        return $locations;
    }

    public function findClientsByCity(string $city): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM location WHERE city = ? AND account_holder_type = "client" ORDER BY id');
        $stmt->execute([$city]);

        $locations = [];
        while ($row = $stmt->fetch()) {
            $locations[] = $this->mapToLocation($row);
        }

        return $locations;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM location ORDER BY id');
        $locations = [];

        while ($row = $stmt->fetch()) {
            $locations[] = $this->mapToLocation($row);
        }

        return $locations;
    }

    public function save(Location $location): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO location (account_holder_id, account_holder_type, city, state, postal_code, address)
            VALUES (?, ?, ?, ?, ?, ?)
        ');

        $stmt->execute([
            $location->getAccountHolderId(),
            $location->getAccountHolderType(),
            $location->getCity(),
            $location->getState(),
            $location->getPostalCode(),
            $location->getAddress()
        ]);

        $id = (int) $this->pdo->lastInsertId();
        $ref = new ReflectionClass(Location::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($location, $id);
    }

    public function update(Location $location): void
    {
        $stmt = $this->pdo->prepare('
            UPDATE location
            SET city = ?, state = ?, postal_code = ?, address = ?
            WHERE id = ?
        ');

        $stmt->execute([
            $location->getCity(),
            $location->getState(),
            $location->getPostalCode(),
            $location->getAddress(),
            $location->getId()
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM location WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function deleteByAccountHolder(int $accountHolderId, string $accountHolderType): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM location WHERE account_holder_id = ? AND account_holder_type = ?');
        $stmt->execute([$accountHolderId, $accountHolderType]);
    }

    private function mapToLocation(array $data): Location
    {
        $location = new Location(
            (int)$data['account_holder_id'],
            $data['account_holder_type'],
            $data['city'],
            $data['state'],
            $data['postal_code'],
            $data['address']
        );

        $ref = new ReflectionClass(Location::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($location, (int) $data['id']);

        return $location;
    }
}
