<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Repository;

use Soosuuke\IaPlatform\Config\Database;

class ProviderLanguageRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function findByProviderId(int $providerId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT l.* FROM language l
            INNER JOIN provider_language pl ON pl.language_id = l.id
            WHERE pl.provider_id = ?
            ORDER BY l.name
        ');
        $stmt->execute([$providerId]);

        $languages = [];
        while ($row = $stmt->fetch()) {
            $languages[] = $row;
        }

        return $languages;
    }

    public function addLanguageToProvider(int $providerId, int $languageId): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO provider_language (provider_id, language_id) VALUES (?, ?)');
        $stmt->execute([$providerId, $languageId]);
    }

    public function removeLanguageFromProvider(int $providerId, int $languageId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM provider_language WHERE provider_id = ? AND language_id = ?');
        $stmt->execute([$providerId, $languageId]);
    }

    public function removeAllLanguagesFromProvider(int $providerId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM provider_language WHERE provider_id = ?');
        $stmt->execute([$providerId]);
    }
}
