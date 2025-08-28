<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Controller;

use Soosuuke\IaPlatform\Entity\ProviderDiploma;
use Soosuuke\IaPlatform\Repository\ProviderDiplomaRepository;
use DateTime;

class ProviderDiplomaController
{
    private ProviderDiplomaRepository $repo;

    public function __construct()
    {
        $this->repo = new ProviderDiplomaRepository();
    }

    // GET /provider-diplomas
    public function index(): void
    {
        
        $providerId = $_SESSION['provider_id'] ?? null;
        if (!$providerId) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $diplomas = $this->repo->findAllByProviderId($providerId);
        echo json_encode(array_map(fn($diploma) => [
            'id' => $diploma->getId(),
            'providerId' => $diploma->getProviderId(),
            'title' => $diploma->getTitle(),
            'institution' => $diploma->getInstitution(),
            'description' => $diploma->getDescription(),
            'startDate' => $diploma->getStartDate()?->format('Y-m-d'),
            'endDate' => $diploma->getEndDate()?->format('Y-m-d'),
        ], $diplomas));
        exit;
    }

    // GET /provider-diplomas/{id}
    public function show(int $id): void
    {
        $diploma = $this->repo->findById($id);
        if (!$diploma) {
            http_response_code(404);
            echo json_encode(['error' => 'Diploma not found']);
            exit;
        }

        echo json_encode([
            'id' => $diploma->getId(),
            'providerId' => $diploma->getProviderId(),
            'title' => $diploma->getTitle(),
            'institution' => $diploma->getInstitution(),
            'description' => $diploma->getDescription(),
            'startDate' => $diploma->getStartDate()?->format('Y-m-d'),
            'endDate' => $diploma->getEndDate()?->format('Y-m-d'),
        ]);
        exit;
    }

    // POST /provider-diplomas
    public function store(): void
    {
        
        $providerId = $_SESSION['provider_id'] ?? null;

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

        if (empty($data['title']) || empty($data['institution'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            exit;
        }

        if (strlen($data['title']) > 255) {
            http_response_code(400);
            echo json_encode(['error' => 'Title must be 255 characters or less']);
            exit;
        }
        if (strlen($data['institution']) > 255) {
            http_response_code(400);
            echo json_encode(['error' => 'Institution must be 255 characters or less']);
            exit;
        }
        if (isset($data['description']) && strlen($data['description']) > 1000) {
            http_response_code(400);
            echo json_encode(['error' => 'Description must be 1000 characters or less']);
            exit;
        }

        try {
            $startDate = !empty($data['startDate']) ? new DateTime($data['startDate']) : null;
            $endDate = !empty($data['endDate']) ? new DateTime($data['endDate']) : null;
            if ($startDate && $endDate && $startDate > $endDate) {
                http_response_code(400);
                echo json_encode(['error' => 'Start date must be before or equal to end date']);
                exit;
            }
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid date format']);
            exit;
        }

        $diploma = new ProviderDiploma(
            0,
            $data['title'],
            $data['institution'],
            $data['description'] ?? null,
            $startDate,
            $endDate,
            $providerId
        );

        $this->repo->save($diploma);

        http_response_code(201);
        echo json_encode(['message' => 'Diploma created', 'id' => $diploma->getId()]);
        exit;
    }

    // PATCH /provider-diplomas/{id}
    public function patch(int $id): void
    {
        
        $providerId = $_SESSION['provider_id'] ?? null;

        $diploma = $this->repo->findById($id);
        if (!$diploma || $diploma->getProviderId() !== $providerId) {
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
        if (isset($data['institution']) && strlen($data['institution']) > 255) {
            http_response_code(400);
            echo json_encode(['error' => 'Institution must be 255 characters or less']);
            exit;
        }
        if (isset($data['description']) && strlen($data['description']) > 1000) {
            http_response_code(400);
            echo json_encode(['error' => 'Description must be 1000 characters or less']);
            exit;
        }

        if (isset($data['startDate']) || isset($data['endDate'])) {
            try {
                $startDate = isset($data['startDate']) ? new DateTime($data['startDate']) : $diploma->getStartDate();
                $endDate = isset($data['endDate']) ? ($data['endDate'] ? new DateTime($data['endDate']) : null) : $diploma->getEndDate();
                if ($startDate && $endDate && $startDate > $endDate) {
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
            $diploma->setTitle($data['title']);
        }
        if (isset($data['institution'])) {
            $diploma->setInstitution($data['institution']);
        }
        if (array_key_exists('description', $data)) {
            $diploma->setDescription($data['description']);
        }
        if (isset($data['startDate'])) {
            $diploma->setStartDate($startDate);
        }
        if (array_key_exists('endDate', $data)) {
            $diploma->setEndDate($endDate);
        }

        $this->repo->update($diploma);
        echo json_encode(['message' => 'Diploma updated']);
        exit;
    }

    // DELETE /provider-diplomas/{id}
    public function destroy(int $id): void
    {
        
        $providerId = $_SESSION['provider_id'] ?? null;

        $diploma = $this->repo->findById($id);
        if (!$diploma || $diploma->getProviderId() !== $providerId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $this->repo->delete($id);
        echo json_encode(['message' => 'Diploma deleted']);
        exit;
    }
}
