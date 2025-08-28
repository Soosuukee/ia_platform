<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Repository;

use Soosuuke\IaPlatform\Entity\Language;
use Soosuuke\IaPlatform\Config\Database;
use ReflectionClass;

class LanguageRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function findById(int $id): ?Language
    {
        $stmt = $this->pdo->prepare('SELECT * FROM language WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        return $data ? $this->mapToLanguage($data) : null;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM language ORDER BY name');
        $languages = [];

        while ($row = $stmt->fetch()) {
            $languages[] = $this->mapToLanguage($row);
        }

        return $languages;
    }

    public function save(Language $language): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO language (name) VALUES (?)');
        $stmt->execute([$language->getName()]);

        $id = (int) $this->pdo->lastInsertId();
        $ref = new ReflectionClass(Language::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($language, $id);
    }

    public function update(Language $language): void
    {
        $stmt = $this->pdo->prepare('UPDATE language SET name = ? WHERE id = ?');
        $stmt->execute([$language->getName(), $language->getId()]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM language WHERE id = ?');
        $stmt->execute([$id]);
    }

    private function mapToLanguage(array $data): Language
    {
        $language = new Language($data['name']);

        $ref = new ReflectionClass(Language::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($language, (int) $data['id']);

        return $language;
    }
}
