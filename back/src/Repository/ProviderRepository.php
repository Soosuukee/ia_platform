<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Repository;

use Soosuuke\IaPlatform\Entity\Provider;
use Soosuuke\IaPlatform\Config\Database;
use Soosuuke\IaPlatform\Repository\AvailabilitySlotRepository;
use DateTimeImmutable;
use ReflectionClass;

class ProviderRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function findById(int $id): ?Provider
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `provider` WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        return $data ? $this->mapToProvider($data) : null;
    }

    public function findByEmail(string $email): ?Provider
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `provider` WHERE email = ?');
        $stmt->execute([$email]);
        $data = $stmt->fetch();

        return $data ? $this->mapToProvider($data) : null;
    }

    public function findBySlug(string $slug): ?Provider
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `provider` WHERE slug = ?');
        $stmt->execute([$slug]);
        $data = $stmt->fetch();

        return $data ? $this->mapToProvider($data) : null;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM `provider`');
        $providers = [];

        while ($row = $stmt->fetch()) {
            $providers[] = $this->mapToProvider($row);
        }

        return $providers;
    }

    public function save(Provider $provider): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO `provider` (
                first_name, last_name, email, password, country_id, city, state, postal_code, address,
                profile_picture, joined_at, slug
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');

        $stmt->execute([
            $provider->getFirstName(),
            $provider->getLastName(),
            $provider->getEmail(),
            $provider->getPassword(),
            $provider->getCountryId(),
            $provider->getCity(),
            $provider->getState(),
            $provider->getPostalCode(),
            $provider->getAddress(),
            $provider->getProfilePicture(),
            $provider->getJoinedAt()->format('Y-m-d H:i:s'),
            $provider->getSlug()
        ]);

        $providerId = (int) $this->pdo->lastInsertId();

        $ref = new ReflectionClass(Provider::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($provider, $providerId);
    }

    public function update(Provider $provider): void
    {
        $stmt = $this->pdo->prepare('
            UPDATE `provider` SET
                first_name = ?,
                last_name = ?,
                email = ?,
                password = ?,
                country_id = ?,
                city = ?,
                state = ?,
                postal_code = ?,
                address = ?,
                profile_picture = ?,
                slug = ?
            WHERE id = ?
        ');

        $stmt->execute([
            $provider->getFirstName(),
            $provider->getLastName(),
            $provider->getEmail(),
            $provider->getPassword(),
            $provider->getCountryId(),
            $provider->getCity(),
            $provider->getState(),
            $provider->getPostalCode(),
            $provider->getAddress(),
            $provider->getProfilePicture(),
            $provider->getSlug(),
            $provider->getId()
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM `provider` WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function findSkillsByProviderId(int $providerId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT s.* FROM skill s
            INNER JOIN provider_skill ps ON ps.skill_id = s.id
            WHERE ps.provider_id = ?
        ');
        $stmt->execute([$providerId]);

        $skills = [];
        while ($row = $stmt->fetch()) {
            $skills[] = $row['name']; // ou un objet Skill si tu préfères
        }

        return $skills;
    }

    private function mapToProvider(array $data): Provider
    {
        $provider = new Provider(
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['password'],
            (int) $data['country_id'],
            $data['city'],
            $data['profile_picture'] ?? null,
            $data['slug'] ?? null,
            $data['state'] ?? null,
            $data['postal_code'] ?? null,
            $data['address'] ?? null
        );

        $ref = new ReflectionClass(Provider::class);

        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($provider, (int) $data['id']);

        $joinedAtProp = $ref->getProperty('joinedAt');
        $joinedAtProp->setAccessible(true);
        $joinedAtProp->setValue($provider, new DateTimeImmutable($data['joined_at']));

        return $provider;
    }
}
