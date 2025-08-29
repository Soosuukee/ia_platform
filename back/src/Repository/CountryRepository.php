<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Repository;

use Soosuuke\IaPlatform\Entity\Country;
use Soosuuke\IaPlatform\Config\Database;
use ReflectionClass;

class CountryRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function findById(int $id): ?Country
    {
        $stmt = $this->pdo->prepare('SELECT * FROM country WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        return $data ? $this->mapToCountry($data) : null;
    }



    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM country ORDER BY name');
        $countries = [];

        while ($row = $stmt->fetch()) {
            $countries[] = $this->mapToCountry($row);
        }

        return $countries;
    }

    public function save(Country $country): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO country (name) VALUES (?)');
        $stmt->execute([$country->getName()]);

        $id = (int) $this->pdo->lastInsertId();
        $ref = new ReflectionClass(Country::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($country, $id);
    }

    public function update(Country $country): void
    {
        $stmt = $this->pdo->prepare('UPDATE country SET name = ? WHERE id = ?');
        $stmt->execute([$country->getName(), $country->getId()]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM country WHERE id = ?');
        $stmt->execute([$id]);
    }

    private function mapToCountry(array $data): Country
    {
        $country = new Country($data['name']);

        $ref = new ReflectionClass(Country::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($country, (int) $data['id']);

        return $country;
    }
}
