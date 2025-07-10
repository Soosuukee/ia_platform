<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Repository;

use Soosuuke\IaPlatform\Entity\Provider;
use Soosuuke\IaPlatform\Config\Database;
use Soosuuke\IaPlatform\Repository\UserRepository;
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

    public function findByUserId(int $userId): ?Provider
    {
        $stmt = $this->pdo->prepare('SELECT * FROM provider WHERE user_id = ?');
        $stmt->execute([$userId]);
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
        INSERT INTO provider (user_id, bio, created_at)
        VALUES (?, ?, ?)
    ');

        $stmt->execute([
            $provider->getUser()->getId(),
            $provider->getBio(),
            $provider->getCreatedAt()->format('Y-m-d H:i:s'),
        ]);

        $providerId = (int) $this->pdo->lastInsertId();

        // Injecter l'ID dans l'objet Provider
        $ref = new ReflectionClass(Provider::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($provider, $providerId);

        // Enregistrer les slots si présents
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
        $userRepo = new UserRepository();
        $slotRepo = new AvailabilitySlotRepository();

        $user = $userRepo->findById((int) $data['user_id']);
        $provider = new Provider($user, $data['bio']);

        // injecter l'ID
        $ref = new ReflectionClass(Provider::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($provider, (int) $data['id']);

        // injecter createdAt
        $createdAtProp = $ref->getProperty('createdAt');
        $createdAtProp->setAccessible(true);
        $createdAtProp->setValue($provider, new DateTimeImmutable($data['created_at']));

        // injecter les créneaux de disponibilité
        $slots = $slotRepo->findAllByProviderId((int)$data['id']);
        $provider->setAvailabilitySlots($slots);

        return $provider;
    }
}
