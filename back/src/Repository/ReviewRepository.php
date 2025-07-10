<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Repository;

use Soosuuke\IaPlatform\Config\Database;
use Soosuuke\IaPlatform\Entity\Review;
use Soosuuke\IaPlatform\Repository\ProviderRepository;
use Soosuuke\IaPlatform\Repository\UserRepository;
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

    public function findAllByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM review WHERE user_id = ? ORDER BY created_at DESC');
        $stmt->execute([$userId]);

        $reviews = [];
        while ($row = $stmt->fetch()) {
            $reviews[] = $this->mapToReview($row);
        }

        return $reviews;
    }

    public function save(Review $review): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO review (user_id, provider_id, content, rating, created_at)
            VALUES (?, ?, ?, ?, ?)
        ');

        $stmt->execute([
            $review->getUser()->getId(),
            $review->getProvider()->getId(),
            $review->getContent(),
            $review->getRating(),
            $review->getCreatedAt()->format('Y-m-d H:i:s')
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM review WHERE id = ?');
        $stmt->execute([$id]);
    }

    private function mapToReview(array $data): Review
    {
        $userRepo = new UserRepository();
        $providerRepo = new ProviderRepository();

        $user = $userRepo->findById((int) $data['user_id']);
        $provider = $providerRepo->findById((int) $data['provider_id']);

        $review = new Review(
            $user,
            $provider,
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
