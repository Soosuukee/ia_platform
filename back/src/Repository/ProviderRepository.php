<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Repository;

use Soosuuke\IaPlatform\Entity\Provider;
use Soosuuke\IaPlatform\Config\Database;
use Soosuuke\IaPlatform\Repository\AvailabilitySlotRepository;
use Soosuuke\IaPlatform\Repository\SocialLinkRepository;
use DateTimeImmutable;
use ReflectionClass;

class ProviderRepository
{
    private \PDO $pdo;
    private SocialLinkRepository $socialLinkRepository;

    public function __construct()
    {
        $this->pdo = Database::connect();
        $this->socialLinkRepository = new SocialLinkRepository();
    }

    public function findById(int $id): ?Provider
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `provider` WHERE id = ?');
        $stmt->execute([$id]);
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
                first_name, last_name, email, password, job_id, country_id, city, state, postal_code, address,
                profile_picture, joined_at, slug
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');

        $stmt->execute([
            $provider->getFirstName(),
            $provider->getLastName(),
            $provider->getEmail(),
            $provider->getPassword(),
            $provider->getJobId(),
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
                job_id = ?,
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
            $provider->getJobId(),
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

    public function findByCountry(int $countryId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `provider` WHERE country_id = ?');
        $stmt->execute([$countryId]);
        $providers = [];

        while ($row = $stmt->fetch()) {
            $providers[] = $this->mapToProvider($row);
        }

        return $providers;
    }

    public function findByJob(int $jobId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `provider` WHERE job_id = ?');
        $stmt->execute([$jobId]);
        $providers = [];

        while ($row = $stmt->fetch()) {
            $providers[] = $this->mapToProvider($row);
        }

        return $providers;
    }

    public function findByHardSkill(string $skillName): array
    {
        $stmt = $this->pdo->prepare('
            SELECT DISTINCT p.* FROM `provider` p
            INNER JOIN provider_hard_skills phs ON p.id = phs.provider_id
            INNER JOIN hard_skill hs ON phs.hard_skill_id = hs.id
            WHERE hs.title LIKE ?
        ');
        $stmt->execute(['%' . $skillName . '%']);
        $providers = [];

        while ($row = $stmt->fetch()) {
            $providers[] = $this->mapToProvider($row);
        }

        return $providers;
    }

    public function findBySoftSkill(string $skillName): array
    {
        $stmt = $this->pdo->prepare('
            SELECT DISTINCT p.* FROM `provider` p
            INNER JOIN provider_soft_skills pss ON p.id = pss.provider_id
            INNER JOIN soft_skill ss ON pss.soft_skill_id = ss.id
            WHERE ss.title LIKE ?
        ');
        $stmt->execute(['%' . $skillName . '%']);
        $providers = [];

        while ($row = $stmt->fetch()) {
            $providers[] = $this->mapToProvider($row);
        }

        return $providers;
    }

    public function findByLanguage(string $languageName): array
    {
        $stmt = $this->pdo->prepare('
            SELECT DISTINCT p.* FROM `provider` p
            INNER JOIN provider_language pl ON p.id = pl.provider_id
            INNER JOIN language l ON pl.language_id = l.id
            WHERE l.name LIKE ?
        ');
        $stmt->execute(['%' . $languageName . '%']);
        $providers = [];

        while ($row = $stmt->fetch()) {
            $providers[] = $this->mapToProvider($row);
        }

        return $providers;
    }

    public function search(string $query): array
    {
        $stmt = $this->pdo->prepare('
            SELECT DISTINCT p.* FROM `provider` p
            LEFT JOIN provider_hard_skills phs ON p.id = phs.provider_id
            LEFT JOIN hard_skill hs ON phs.hard_skill_id = hs.id
            LEFT JOIN provider_soft_skills pss ON p.id = pss.provider_id
            LEFT JOIN soft_skill ss ON pss.soft_skill_id = ss.id
            LEFT JOIN provider_language pl ON p.id = pl.provider_id
            LEFT JOIN language l ON pl.language_id = l.id
            WHERE p.first_name LIKE ? 
               OR p.last_name LIKE ? 
               OR p.city LIKE ?
               OR hs.title LIKE ?
               OR ss.title LIKE ?
               OR l.name LIKE ?
        ');
        $searchTerm = '%' . $query . '%';
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        $providers = [];

        while ($row = $stmt->fetch()) {
            $providers[] = $this->mapToProvider($row);
        }

        return $providers;
    }

    public function findByIdWithSocialLinks(int $id): ?array
    {
        $provider = $this->findById($id);
        if (!$provider) {
            return null;
        }

        $socialLinks = $this->socialLinkRepository->findByProviderId($id);

        return [
            'provider' => $provider,
            'socialLinks' => $socialLinks
        ];
    }

    public function findBySlugWithSocialLinks(string $slug): ?array
    {
        $provider = $this->findBySlug($slug);
        if (!$provider) {
            return null;
        }

        $socialLinks = $this->socialLinkRepository->findByProviderId($provider->getId());

        return [
            'provider' => $provider,
            'socialLinks' => $socialLinks
        ];
    }

    private function mapToProvider(array $data): Provider
    {
        $provider = new Provider(
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['password'],
            (int) $data['job_id'],
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
