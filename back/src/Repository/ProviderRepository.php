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

        if (!$data) {
            return null;
        }

        $provider = $this->mapToProvider($data);

        // On hydrate avec les skills
        $skillRepo = new ProviderSkillRepository();
        $provider->setSkills($skillRepo->findAllSkillsByProviderId($provider->getId()));

        // On hydrate avec les créneaux
        $slotRepo = new AvailabilitySlotRepository();
        $provider->setAvailabilitySlots($slotRepo->findAllByProviderId($provider->getId()));

        return $provider;
    }

    public function findByEmail(string $email): ?Provider
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `provider` WHERE email = ?');
        $stmt->execute([$email]);
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
                first_name, last_name,
                email, password, country, profile_picture, role,
                title, presentation,
                created_at, social_links
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');

        $stmt->execute([
            $provider->getFirstName(),
            $provider->getLastName(),
            $provider->getEmail(),
            $provider->getPassword(),
            $provider->getCountry(),
            $provider->getProfilePicture(),
            $provider->getRole(),
            $provider->getTitle(),
            $provider->getPresentation(),
            $provider->getCreatedAt()->format('Y-m-d H:i:s'),
            json_encode($provider->getSocialLinks())
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

    public function update(Provider $provider): void
    {
        $stmt = $this->pdo->prepare('
            UPDATE `provider` SET
                first_name = ?,
                last_name = ?,
                email = ?,
                password = ?,
                country = ?,
                profile_picture = ?,
                role = ?,
                title = ?,
                presentation = ?,
                social_links = ?
            WHERE id = ?
        ');

        $stmt->execute([
            $provider->getFirstName(),
            $provider->getLastName(),
            $provider->getEmail(),
            $provider->getPassword(),
            $provider->getCountry(),
            $provider->getProfilePicture(),
            $provider->getRole(),
            $provider->getTitle(),
            $provider->getPresentation(),
            json_encode($provider->getSocialLinks()),
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
        // Décoder les liens sociaux depuis la base
        $socialLinks = isset($data['social_links']) && $data['social_links']
            ? json_decode($data['social_links'], true)
            : [];

        $provider = new Provider(
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['password'],
            $data['title'],
            $data['presentation'],
            $data['country'],
            $data['profile_picture'],
            $data['role'] ?? 'provider',
            $socialLinks
        );

        $ref = new ReflectionClass(Provider::class);

        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($provider, (int) $data['id']);

        $createdAtProp = $ref->getProperty('createdAt');
        $createdAtProp->setAccessible(true);
        $createdAtProp->setValue($provider, new DateTimeImmutable($data['created_at']));

        return $provider;
    }
}
