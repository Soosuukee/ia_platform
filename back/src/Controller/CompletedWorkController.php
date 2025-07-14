<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Controller;

use Soosuuke\IaPlatform\Entity\CompletedWork;
use Soosuuke\IaPlatform\Entity\CompletedWorkMedia;
use Soosuuke\IaPlatform\Repository\CompletedWorkRepository;
use Soosuuke\IaPlatform\Repository\CompletedWorkMediaRepository;

class CompletedWorkController
{
    private CompletedWorkRepository $workRepo;
    private CompletedWorkMediaRepository $mediaRepo;

    public function __construct()
    {
        $this->workRepo = new CompletedWorkRepository();
        $this->mediaRepo = new CompletedWorkMediaRepository();
    }

    // GET /completed-works
    public function index(): void
    {
        $sessionProviderId = $_SESSION['provider_id'] ?? null;

        if (!$sessionProviderId) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $works = $this->workRepo->findAllByProviderId($sessionProviderId);

        $data = array_map(fn($work) => [
            'id' => $work->getId(),
            'providerId' => $work->getProviderId(),
            'title' => $work->getTitle(),
            'description' => $work->getDescription(),
            'completedAt' => $work->getCompletedAt()->format('Y-m-d H:i:s'),
        ], $works);

        echo json_encode($data);
    }

    // GET /completed-works/{id} (public)
    public function show(int $id): void
    {
        $work = $this->workRepo->findById($id);

        if (!$work) {
            http_response_code(404);
            echo json_encode(['error' => 'Work not found']);
            return;
        }

        $media = $this->mediaRepo->findAllByWorkId($id);

        echo json_encode([
            'id' => $work->getId(),
            'providerId' => $work->getProviderId(),
            'title' => $work->getTitle(),
            'description' => $work->getDescription(),
            'completedAt' => $work->getCompletedAt()->format('Y-m-d H:i:s'),
            'media' => array_map(fn($m) => [
                'id' => $m->getId(),
                'mediaType' => $m->getMediaType(),
                'mediaUrl' => $m->getMediaUrl()
            ], $media)
        ]);
    }

    // POST /completed-works
    public function store(): void
    {
        $sessionProviderId = $_SESSION['provider_id'] ?? null;
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$sessionProviderId || empty($data['title']) || empty($data['description'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing fields or unauthorized']);
            return;
        }

        $work = new CompletedWork(
            $sessionProviderId,
            $data['title'],
            $data['description']
        );

        $this->workRepo->save($work);

        if (!empty($data['media']) && is_array($data['media'])) {
            foreach ($data['media'] as $mediaData) {
                if (!empty($mediaData['mediaType']) && !empty($mediaData['mediaUrl'])) {
                    $media = new CompletedWorkMedia(
                        $work->getId(),
                        $mediaData['mediaType'],
                        $mediaData['mediaUrl']
                    );
                    $this->mediaRepo->save($media);
                }
            }
        }

        http_response_code(201);
        echo json_encode(['message' => 'Completed work created', 'id' => $work->getId()]);
    }

    // POST /completed-works/{id}/media
    public function addMedia(int $id): void
    {
        $sessionProviderId = $_SESSION['provider_id'] ?? null;
        $work = $this->workRepo->findById($id);

        if (!$work || $work->getProviderId() !== $sessionProviderId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['mediaType']) || empty($data['mediaUrl'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing mediaType or mediaUrl']);
            return;
        }

        $media = new CompletedWorkMedia(
            $work->getId(),
            $data['mediaType'],
            $data['mediaUrl']
        );

        $this->mediaRepo->save($media);

        http_response_code(201);
        echo json_encode(['message' => 'Media added']);
    }

    // PATCH /completed-works/{id}
    public function patch(int $id): void
    {
        $sessionProviderId = $_SESSION['provider_id'] ?? null;
        $work = $this->workRepo->findById($id);

        if (!$work || $work->getProviderId() !== $sessionProviderId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['title'])) {
            $work->setTitle($data['title']);
        }
        if (isset($data['description'])) {
            $work->setDescription($data['description']);
        }

        $this->workRepo->update($work);

        echo json_encode(['message' => 'Completed work updated']);
    }

    // DELETE /completed-works/{id}
    public function destroy(int $id): void
    {
        $sessionProviderId = $_SESSION['provider_id'] ?? null;
        $work = $this->workRepo->findById($id);

        if (!$work || $work->getProviderId() !== $sessionProviderId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $this->workRepo->delete($id);
        echo json_encode(['message' => 'Completed work deleted']);
    }
}
