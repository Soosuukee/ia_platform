<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Repository;

use Soosuuke\IaPlatform\Entity\Tag;
use Soosuuke\IaPlatform\Config\Database;
use ReflectionClass;

class TagRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function findById(int $id): ?Tag
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tag WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        return $data ? $this->mapToTag($data) : null;
    }

    public function findBySlug(string $slug): ?Tag
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tag WHERE slug = ?');
        $stmt->execute([$slug]);
        $data = $stmt->fetch();

        return $data ? $this->mapToTag($data) : null;
    }

    public function findByTitle(string $title): ?Tag
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tag WHERE title = ?');
        $stmt->execute([$title]);
        $data = $stmt->fetch();

        return $data ? $this->mapToTag($data) : null;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM tag ORDER BY title');
        $tags = [];

        while ($row = $stmt->fetch()) {
            $tags[] = $this->mapToTag($row);
        }

        return $tags;
    }

    public function save(Tag $tag): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO tag (title, slug) VALUES (?, ?)');
        $stmt->execute([$tag->getTitle(), $tag->getSlug()]);

        $id = (int) $this->pdo->lastInsertId();
        $ref = new ReflectionClass(Tag::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($tag, $id);
    }

    public function update(Tag $tag): void
    {
        $stmt = $this->pdo->prepare('UPDATE tag SET title = ?, slug = ? WHERE id = ?');
        $stmt->execute([$tag->getTitle(), $tag->getSlug(), $tag->getId()]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM tag WHERE id = ?');
        $stmt->execute([$id]);
    }

    private function mapToTag(array $data): Tag
    {
        $tag = new Tag($data['title'], (int) $data['id'], $data['slug'] ?? null);
        return $tag;
    }
}
