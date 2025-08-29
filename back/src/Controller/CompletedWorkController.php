<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Controller;

use Soosuuke\IaPlatform\Entity\CompletedWork;
use Soosuuke\IaPlatform\Entity\CompletedWorkMedia;
use Soosuuke\IaPlatform\Repository\CompletedWorkRepository;
use Soosuuke\IaPlatform\Repository\CompletedWorkMediaRepository;
use DateTimeImmutable;
use Soosuuke\IaPlatform\Config\AuthMiddleware;

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
        $works = $this->workRepo->findAll();

        $response = array_map(fn($work) => [
            'id' => $work->getId(),
            'providerId' => $work->getProviderId(),
            'company' => $work->getCompany(),
            'title' => $work->getTitle(),
            'description' => $work->getDescription(),
            'startDate' => $work->getStartDate()->format('Y-m-d'),
            'endDate' => $work->getEndDate()?->format('Y-m-d'),
        ], $works);

        echo json_encode($response);
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

    // POST /completed-works
    public function store(): void
    {

        $providerId = AuthMiddleware::getCurrentUserType() === 'provider' ? AuthMiddleware::getCurrentUserId() : null;

        if (!$providerId) {
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

        if (empty($data['title']) || empty($data['description']) || empty($data['company']) || empty($data['startDate'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            exit;
        }

        if (strlen($data['title']) > 255) {
            http_response_code(400);
            echo json_encode(['error' => 'Title must be 255 characters or less']);
            exit;
        }
        if (strlen($data['company']) > 255) {
            http_response_code(400);
            echo json_encode(['error' => 'Company must be 255 characters or less']);
            exit;
        }
        if (strlen($data['description']) > 1000) {
            http_response_code(400);
            echo json_encode(['error' => 'Description must be 1000 characters or less']);
            exit;
        }

        try {
            $startDate = new DateTimeImmutable($data['startDate']);
            $endDate = !empty($data['endDate']) ? new DateTimeImmutable($data['endDate']) : null;
            if ($endDate && $startDate > $endDate) {
                http_response_code(400);
                echo json_encode(['error' => 'Start date must be before or equal to end date']);
                exit;
            }
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid date format']);
            exit;
        }

        if (isset($data['media']) && is_array($data['media'])) {
            foreach ($data['media'] as $m) {
                if (!empty($m['mediaUrl']) && !filter_var($m['mediaUrl'], FILTER_VALIDATE_URL)) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Invalid media URL']);
                    exit;
                }
                if (!empty($m['mediaType']) && !in_array($m['mediaType'], ['image', 'video'])) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Invalid media type']);
                    exit;
                }
            }
        }

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
        echo json_encode(['message' => 'Work created', 'id' => $work->getId()]);
        exit;
    }

    // PATCH /completed-works/{id}
    public function patch(int $id): void
    {

        $providerId = AuthMiddleware::getCurrentUserType() === 'provider' ? AuthMiddleware::getCurrentUserId() : null;

        $work = $this->workRepo->findById($id);
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

        if (isset($data['title']) && strlen($data['title']) > 255) {
            http_response_code(400);
            echo json_encode(['error' => 'Title must be 255 characters or less']);
            exit;
        }
        if (isset($data['company']) && strlen($data['company']) > 255) {
            http_response_code(400);
            echo json_encode(['error' => 'Company must be 255 characters or less']);
            exit;
        }
        if (isset($data['description']) && strlen($data['description']) > 1000) {
            http_response_code(400);
            echo json_encode(['error' => 'Description must be 1000 characters or less']);
            exit;
        }

        if (isset($data['startDate']) || isset($data['endDate'])) {
            try {
                $startDate = isset($data['startDate']) ? new DateTimeImmutable($data['startDate']) : $work->getStartDate();
                $endDate = isset($data['endDate']) ? ($data['endDate'] ? new DateTimeImmutable($data['endDate']) : null) : $work->getEndDate();
                if ($endDate && $startDate > $endDate) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Start date must be before or equal to end date']);
                    exit;
                }
            } catch (\Exception $e) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid date format']);
                exit;
            }
        }

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
        echo json_encode(['message' => 'Work updated']);
        exit;
    }

    // DELETE /completed-works/{id}
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
        echo json_encode(['message' => 'Work deleted']);
        exit;
    }
}
