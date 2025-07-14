<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Repository;

use Soosuuke\IaPlatform\Config\Database;
use Soosuuke\IaPlatform\Entity\Review;
use DateTimeImmutable;
use ReflectionClass;

class ReviewRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function findById(int $id): ?Review
    {
        $stmt = $this->pdo->prepare('SELECT * FROM review WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        return $data ? $this->mapToReview($data) : null;
    }

    public function findAllByProviderId(int $providerId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM review WHERE provider_id = ? ORDER BY created_at DESC');
        $stmt->execute([$providerId]);

        $reviews = [];
        while ($row = $stmt->fetch()) {
            $reviews[] = $this->mapToReview($row);
        }

        return $reviews;
    }

    public function findAllByClientId(int $clientId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM review WHERE client_id = ? ORDER BY created_at DESC');
        $stmt->execute([$clientId]);

        $reviews = [];
        while ($row = $stmt->fetch()) {
            $reviews[] = $this->mapToReview($row);
        }

        return $reviews;
    }

    public function save(Review $review): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO review (client_id, provider_id, content, rating, created_at)
            VALUES (?, ?, ?, ?, ?)
        ');

        $stmt->execute([
            $review->getClientId(),
            $review->getProviderId(),
            $review->getContent(),
            $review->getRating(),
            $review->getCreatedAt()->format('Y-m-d H:i:s')
        ]);

        $id = (int) $this->pdo->lastInsertId();
        $ref = new ReflectionClass(Review::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($review, $id);
    }

    public function update(Review $review): void
    {
        $stmt = $this->pdo->prepare('
        UPDATE review
        SET content = ?, rating = ?
        WHERE id = ?
    ');

        $stmt->execute([
            $review->getContent(),
            $review->getRating(),
            $review->getId()
        ]);
    }


    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM review WHERE id = ?');
        $stmt->execute([$id]);
    }

    private function mapToReview(array $data): Review
    {
        $review = new Review(
            (int) $data['client_id'],
            (int) $data['provider_id'],
            $data['content'],
            (int) $data['rating']
        );

        $ref = new ReflectionClass(Review::class);

        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($review, (int) $data['id']);

        $createdAtProp = $ref->getProperty('createdAt');
        $createdAtProp->setAccessible(true);
        $createdAtProp->setValue($review, new DateTimeImmutable($data['created_at']));

        return $review;
    }
}
