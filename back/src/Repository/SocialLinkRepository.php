<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Repository;

use Soosuuke\IaPlatform\Entity\SocialLink;
use Soosuuke\IaPlatform\Config\Database;
use ReflectionClass;

class SocialLinkRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function findById(int $id): ?SocialLink
    {
        $stmt = $this->pdo->prepare('SELECT * FROM social_link WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        return $data ? $this->mapToSocialLink($data) : null;
    }

    public function findByProviderId(int $providerId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM social_link WHERE provider_id = ? ORDER BY id');
        $stmt->execute([$providerId]);

        $socialLinks = [];
        while ($row = $stmt->fetch()) {
            $socialLinks[] = $this->mapToSocialLink($row);
        }

        return $socialLinks;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM social_link ORDER BY id');
        $socialLinks = [];

        while ($row = $stmt->fetch()) {
            $socialLinks[] = $this->mapToSocialLink($row);
        }

        return $socialLinks;
    }

    public function save(SocialLink $socialLink): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO social_link (provider_id, platform, url)
            VALUES (?, ?, ?)
        ');

        $stmt->execute([
            $socialLink->getProviderId(),
            $socialLink->getPlatform(),
            $socialLink->getUrl()
        ]);

        $id = (int) $this->pdo->lastInsertId();
        $ref = new ReflectionClass(SocialLink::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($socialLink, $id);
    }

    public function update(SocialLink $socialLink): void
    {
        $stmt = $this->pdo->prepare('
            UPDATE social_link
            SET provider_id = ?, platform = ?, url = ?
            WHERE id = ?
        ');

        $stmt->execute([
            $socialLink->getProviderId(),
            $socialLink->getPlatform(),
            $socialLink->getUrl(),
            $socialLink->getId()
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM social_link WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function deleteByProviderId(int $providerId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM social_link WHERE provider_id = ?');
        $stmt->execute([$providerId]);
    }

    private function mapToSocialLink(array $data): SocialLink
    {
        $socialLink = new SocialLink(
            (int)$data['provider_id'],
            $data['platform'],
            $data['url']
        );

        $ref = new ReflectionClass(SocialLink::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($socialLink, (int) $data['id']);

        return $socialLink;
    }
}
