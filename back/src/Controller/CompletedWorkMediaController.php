<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Controller;

use Soosuuke\IaPlatform\Entity\CompletedWorkMedia;
use Soosuuke\IaPlatform\Repository\CompletedWorkMediaRepository;
use Soosuuke\IaPlatform\Repository\CompletedWorkRepository;

class CompletedWorkMediaController
{
    private CompletedWorkMediaRepository $mediaRepo;
    private CompletedWorkRepository $workRepo;

    public function __construct()
    {
        $this->mediaRepo = new CompletedWorkMediaRepository();
        $this->workRepo = new CompletedWorkRepository();
    }

    // GET /completed-works/{id}/media
    public function indexByWorkId(int $workId): void
    {
        $media = $this->mediaRepo->findAllByWorkId($workId);

        echo json_encode(array_map(fn($m) => [
            'id' => $m->getId(),
            'workId' => $m->getCompletedWorkId(),
            'mediaType' => $m->getMediaType(),
            'mediaUrl' => $m->getMediaUrl()
        ], $media));
        exit;
    }

    // POST /completed-work-media
    public function store(): void
    {
        
        $providerId = $_SESSION['provider_id'] ?? null;
        $data = json_decode(file_get_contents('php://input'), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON']);
            exit;
        }

        if (!$providerId) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        if (empty($data['workId']) || empty($data['mediaType']) || empty($data['mediaUrl'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            exit;
        }

        if (!in_array($data['mediaType'], ['image', 'video'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid media type. Must be "image" or "video"']);
            exit;
        }

        if (!filter_var($data['mediaUrl'], FILTER_VALIDATE_URL)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid media URL']);
            exit;
        }

        $work = $this->workRepo->findById((int) $data['workId']);
        if (!$work || $work->getProviderId() !== $providerId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $media = new CompletedWorkMedia(
            (int) $data['workId'],
            $data['mediaType'],
            $data['mediaUrl']
        );

        $this->mediaRepo->save($media);

        http_response_code(201);
        echo json_encode([
            'message' => 'Media created',
            'id' => $media->getId()
        ]);
        exit;
    }

    // PATCH /completed-work-media/{id}
    public function patch(int $id): void
    {
        
        $providerId = $_SESSION['provider_id'] ?? null;

        if (!$providerId) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $media = $this->mediaRepo->findById($id);
        if (!$media) {
            http_response_code(404);
            echo json_encode(['error' => 'Media not found']);
            exit;
        }

        $work = $this->workRepo->findById($media->getWorkId());
        if (!$work || $work->getProviderId() !== $providerId) {
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

        if (isset($data['mediaType']) && !in_array($data['mediaType'], ['image', 'video'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid media type. Must be "image" or "video"']);
            exit;
        }

        if (isset($data['mediaUrl']) && !filter_var($data['mediaUrl'], FILTER_VALIDATE_URL)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid media URL']);
            exit;
        }

        if (isset($data['mediaType'])) {
            $media->setMediaType($data['mediaType']);
        }
        if (isset($data['mediaUrl'])) {
            $media->setMediaUrl($data['mediaUrl']);
        }

        $this->mediaRepo->update($media);
        echo json_encode(['message' => 'Media updated']);
        exit;
    }

    // DELETE /completed-work-media/{id}
    public function destroy(int $id): void
    {
        
        $providerId = $_SESSION['provider_id'] ?? null;

        if (!$providerId) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $media = $this->mediaRepo->findById($id);
        if (!$media) {
            http_response_code(404);
            echo json_encode(['error' => 'Media not found']);
            exit;
        }

        $work = $this->workRepo->findById($media->getWorkId());
        if (!$work || $work->getProviderId() !== $providerId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $this->mediaRepo->delete($id);
        echo json_encode(['message' => 'Media deleted']);
        exit;
    }
}
