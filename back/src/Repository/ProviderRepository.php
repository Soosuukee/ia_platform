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
        $stmt = $this->pdo->prepare('SELECT * FROM provider WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        return $data ? $this->mapToProvider($data) : null;
    }

    public function findByEmail(string $email): ?Provider
    {
        $stmt = $this->pdo->prepare('SELECT * FROM provider WHERE email = ?');
        $stmt->execute([$email]);
        $data = $stmt->fetch();

        return $data ? $this->mapToProvider($data) : null;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM provider');
        $providers = [];

        while ($row = $stmt->fetch()) {
            $providers[] = $this->mapToProvider($row);
        }

        return $providers;
    }

    public function save(Provider $provider): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO provider (
                email, password, country, role,
                title, presentation,
                firstname, lastname,
                created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');

        $stmt->execute([
            $provider->getEmail(),
            $provider->getPassword(),
            $provider->getCountry(),
            $provider->getRole(),
            $provider->getTitle(),
            $provider->getPresentation(),
            $provider->getFirstname(),
            $provider->getLastname(),
            $provider->getCreatedAt()->format('Y-m-d H:i:s'),
        ]);

        $providerId = (int) $this->pdo->lastInsertId();

        $ref = new ReflectionClass(Provider::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($provider, $providerId);

        $slotRepo = new AvailabilitySlotRepository();
        foreach ($provider->getAvailabilitySlots() as $slot) {
            $slotRepo->saveForProvider($providerId, $slot);
        }
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM provider WHERE id = ?');
        $stmt->execute([$id]);
    }

    private function mapToProvider(array $data): Provider
    {
        $provider = new Provider(
            $data['email'],
            $data['password'],
            $data['title'],
            $data['presentation'],
            $data['country'],
            $data['firstname'],
            $data['lastname']
        );

        $ref = new ReflectionClass(Provider::class);

        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($provider, (int) $data['id']);

        $createdAtProp = $ref->getProperty('createdAt');
        $createdAtProp->setAccessible(true);
        $createdAtProp->setValue($provider, new DateTimeImmutable($data['created_at']));

        $slotRepo = new AvailabilitySlotRepository();
        $slots = $slotRepo->findAllByProviderId((int) $data['id']);
        $provider->setAvailabilitySlots($slots);

        return $provider;
    }
}
