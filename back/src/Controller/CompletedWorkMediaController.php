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

        if (!$providerId || empty($data['workId']) || empty($data['mediaType']) || empty($data['mediaUrl'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Champs manquants']);
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
        echo json_encode(['message' => 'Média ajouté']);
        exit;
    }

    // PATCH /completed-work-media/{id}
    public function patch(int $id): void
    {
        $providerId = $_SESSION['provider_id'] ?? null;
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

        if (isset($data['mediaType'])) {
            $media->setMediaType($data['mediaType']);
        }
        if (isset($data['mediaUrl'])) {
            $media->setMediaUrl($data['mediaUrl']);
        }

        $this->mediaRepo->update($media);

        echo json_encode(['message' => 'Média mis à jour']);
        exit;
    }

    // DELETE /completed-work-media/{id}
    public function destroy(int $id): void
    {
        $providerId = $_SESSION['provider_id'] ?? null;
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
        echo json_encode(['message' => 'Média supprimé']);
        exit;
    }
}
