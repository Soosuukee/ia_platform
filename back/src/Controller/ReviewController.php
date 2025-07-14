<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Controller;

use Soosuuke\IaPlatform\Entity\Review;
use Soosuuke\IaPlatform\Repository\ReviewRepository;

class ReviewController
{
    private ReviewRepository $reviewRepo;

    public function __construct()
    {
        $this->reviewRepo = new ReviewRepository();
    }

    // GET /providers/{providerId}/reviews (public)
    public function indexByProvider(int $providerId): void
    {
        $reviews = $this->reviewRepo->findAllByProviderId($providerId);

        $data = array_map(fn($review) => $this->serialize($review), $reviews);

        echo json_encode($data);
    }

    // POST /reviews
    public function store(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $clientId = $_SESSION['client_id'] ?? null;

        if (
            !$clientId ||
            empty($data['providerId']) ||
            !isset($data['rating']) ||
            !isset($data['content'])
        ) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing fields']);
            return;
        }

        $review = new Review(
            (int) $clientId,
            (int) $data['providerId'],
            $data['content'],
            (int) $data['rating']
        );

        $this->reviewRepo->save($review);

        http_response_code(201);
        echo json_encode(['message' => 'Review created', 'id' => $review->getId()]);
    }

    // GET /reviews/{id}
    public function show(int $id): void
    {
        $review = $this->reviewRepo->findById($id);

        if (!$review) {
            http_response_code(404);
            echo json_encode(['error' => 'Review not found']);
            return;
        }

        echo json_encode($this->serialize($review));
    }

    // PATCH /reviews/{id}
    public function patch(int $id): void
    {
        $clientId = $_SESSION['client_id'] ?? null;
        $review = $this->reviewRepo->findById($id);

        if (!$review || $review->getClientId() !== $clientId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['content'])) {
            $review->setContent($data['content']);
        }

        if (isset($data['rating'])) {
            $review->setRating((int) $data['rating']);
        }

        $this->reviewRepo->update($review);

        echo json_encode(['message' => 'Review updated']);
    }

    // DELETE /reviews/{id}
    public function destroy(int $id): void
    {
        $clientId = $_SESSION['client_id'] ?? null;
        $review = $this->reviewRepo->findById($id);

        if (!$review || $review->getClientId() !== $clientId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $this->reviewRepo->delete($id);
        echo json_encode(['message' => 'Review deleted']);
    }

    // Private
    private function serialize(Review $review): array
    {
        return [
            'id' => $review->getId(),
            'clientId' => $review->getClientId(),
            'providerId' => $review->getProviderId(),
            'rating' => $review->getRating(),
            'content' => $review->getContent(),
            'createdAt' => $review->getCreatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
