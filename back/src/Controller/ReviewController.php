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
        exit;
    }

    // POST /reviews
    public function store(): void
    {

        $clientId = $_SESSION['client_id'] ?? null;

        if (!$clientId) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON']);
            exit;
        }

        if (empty($data['providerId']) || !isset($data['rating']) || !isset($data['comment'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            exit;
        }

        if (!is_numeric($data['rating']) || $data['rating'] < 1 || $data['rating'] > 5) {
            http_response_code(400);
            echo json_encode(['error' => 'Rating must be between 1 and 5']);
            exit;
        }

        if (strlen($data['comment']) > 1000) {
            http_response_code(400);
            echo json_encode(['error' => 'Comment must be 1000 characters or less']);
            exit;
        }

        if (!is_numeric($data['providerId']) || $data['providerId'] <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid providerId']);
            exit;
        }

        $review = new Review(
            (int) $clientId,
            (int) $data['providerId'],
            $data['comment'],
            (int) $data['rating']
        );

        $this->reviewRepo->save($review);

        http_response_code(201);
        echo json_encode(['message' => 'Review created', 'id' => $review->getId()]);
        exit;
    }

    // GET /reviews/{id}
    public function show(int $id): void
    {
        $review = $this->reviewRepo->findById($id);

        if (!$review) {
            http_response_code(404);
            echo json_encode(['error' => 'Review not found']);
            exit;
        }

        echo json_encode($this->serialize($review));
        exit;
    }

    // PATCH /reviews/{id}
    public function patch(int $id): void
    {

        $clientId = $_SESSION['client_id'] ?? null;

        $review = $this->reviewRepo->findById($id);
        if (!$review || $review->getClientId() !== $clientId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON']);
            exit;
        }

        if (isset($data['comment']) && strlen($data['comment']) > 1000) {
            http_response_code(400);
            echo json_encode(['error' => 'Comment must be 1000 characters or less']);
            exit;
        }

        if (isset($data['rating']) && (!is_numeric($data['rating']) || $data['rating'] < 1 || $data['rating'] > 5)) {
            http_response_code(400);
            echo json_encode(['error' => 'Rating must be between 1 and 5']);
            exit;
        }

        if (isset($data['comment'])) {
            $review->setComment($data['comment']);
        }

        if (isset($data['rating'])) {
            $review->setRating((int) $data['rating']);
        }

        $this->reviewRepo->update($review);
        echo json_encode(['message' => 'Review updated']);
        exit;
    }

    // DELETE /reviews/{id}
    public function destroy(int $id): void
    {

        $clientId = $_SESSION['client_id'] ?? null;

        $review = $this->reviewRepo->findById($id);
        if (!$review || $review->getClientId() !== $clientId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $this->reviewRepo->delete($id);
        echo json_encode(['message' => 'Review deleted']);
        exit;
    }

    // Private
    private function serialize(Review $review): array
    {
        return [
            'id' => $review->getId(),
            'clientId' => $review->getClientId(),
            'providerId' => $review->getProviderId(),
            'rating' => $review->getRating(),
            'comment' => $review->getComment(),
            'createdAt' => $review->getCreatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
