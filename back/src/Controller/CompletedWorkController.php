<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Controller;

use Soosuuke\IaPlatform\Entity\CompletedWork;
use Soosuuke\IaPlatform\Entity\CompletedWorkMedia;
use Soosuuke\IaPlatform\Repository\CompletedWorkRepository;
use Soosuuke\IaPlatform\Repository\CompletedWorkMediaRepository;
use DateTimeImmutable;

class CompletedWorkController
{
    private CompletedWorkRepository $workRepo;
    private CompletedWorkMediaRepository $mediaRepo;

    public function __construct()
    {
        $this->workRepo = new CompletedWorkRepository();
        $this->mediaRepo = new CompletedWorkMediaRepository();
    }

    public function index(): void
    {
        $providerId = $_SESSION['provider_id'] ?? null;
        if (!$providerId) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $works = $this->workRepo->findAllByProviderId($providerId);

        echo json_encode(array_map(fn($work) => [
            'id' => $work->getId(),
            'providerId' => $work->getProviderId(),
            'company' => $work->getCompany(),
            'title' => $work->getTitle(),
            'description' => $work->getDescription(),
            'startDate' => $work->getStartDate()->format('Y-m-d'),
            'endDate' => $work->getEndDate()?->format('Y-m-d'),
        ], $works));
        exit;
    }

    public function show(int $id): void
    {
        $work = $this->workRepo->findById($id);
        if (!$work) {
            http_response_code(404);
            echo json_encode(['error' => 'Work not found']);
            exit;
        }

        $media = $this->mediaRepo->findAllByWorkId($id);

        echo json_encode([
            'id' => $work->getId(),
            'providerId' => $work->getProviderId(),
            'company' => $work->getCompany(),
            'title' => $work->getTitle(),
            'description' => $work->getDescription(),
            'startDate' => $work->getStartDate()->format('Y-m-d'),
            'endDate' => $work->getEndDate()?->format('Y-m-d'),
            'media' => array_map(fn($m) => [
                'id' => $m->getId(),
                'mediaType' => $m->getMediaType(),
                'mediaUrl' => $m->getMediaUrl()
            ], $media)
        ]);
        exit;
    }

    public function store(): void
    {
        $providerId = $_SESSION['provider_id'] ?? null;
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$providerId || empty($data['title']) || empty($data['description']) || empty($data['company']) || empty($data['startDate'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Champs manquants']);
            exit;
        }

        $startDate = new DateTimeImmutable($data['startDate']);
        $endDate = !empty($data['endDate']) ? new DateTimeImmutable($data['endDate']) : null;

        $work = new CompletedWork(
            $providerId,
            $data['company'],
            $data['title'],
            $data['description'],
            $startDate,
            $endDate
        );

        $this->workRepo->save($work);

        if (!empty($data['media']) && is_array($data['media'])) {
            foreach ($data['media'] as $m) {
                if (!empty($m['mediaType']) && !empty($m['mediaUrl'])) {
                    $media = new CompletedWorkMedia($work->getId(), $m['mediaType'], $m['mediaUrl']);
                    $this->mediaRepo->save($media);
                }
            }
        }

        http_response_code(201);
        echo json_encode(['message' => 'Work enregistré', 'id' => $work->getId()]);
        exit;
    }

    public function patch(int $id): void
    {
        $providerId = $_SESSION['provider_id'] ?? null;
        $work = $this->workRepo->findById($id);

        if (!$work || $work->getProviderId() !== $providerId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['title'])) {
            $work->setTitle($data['title']);
        }
        if (isset($data['description'])) {
            $work->setDescription($data['description']);
        }
        if (isset($data['company'])) {
            $work->setCompany($data['company']);
        }
        if (isset($data['startDate'])) {
            $work->setStartDate(new DateTimeImmutable($data['startDate']));
        }
        if (array_key_exists('endDate', $data)) {
            $work->setEndDate($data['endDate'] ? new DateTimeImmutable($data['endDate']) : null);
        }

        $this->workRepo->update($work);
        echo json_encode(['message' => 'Work modifié']);
        exit;
    }

    public function destroy(int $id): void
    {
        $providerId = $_SESSION['provider_id'] ?? null;
        $work = $this->workRepo->findById($id);

        if (!$work || $work->getProviderId() !== $providerId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $this->workRepo->delete($id);
        echo json_encode(['message' => 'Work supprimé']);
        exit;
    }
}
