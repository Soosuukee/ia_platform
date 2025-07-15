<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Controller;

use Soosuuke\IaPlatform\Entity\ProviderDiploma;
use Soosuuke\IaPlatform\Repository\ProviderDiplomaRepository;
use DateTimeImmutable;

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
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$providerId || empty($data['title']) || empty($data['institution'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Champs obligatoires manquants']);
            exit;
        }

        $startDate = !empty($data['startDate']) ? new DateTimeImmutable($data['startDate']) : null;
        $endDate = !empty($data['endDate']) ? new DateTimeImmutable($data['endDate']) : null;

        $diploma = new ProviderDiploma(
            $data['title'],
            $data['institution'],
            $data['description'] ?? null,
            $startDate,
            $endDate,
            $providerId
        );

        $this->repo->save($diploma);

        http_response_code(201);
        echo json_encode(['message' => 'Diplôme créé', 'id' => $diploma->getId()]);
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
            $diploma->setStartDate(new DateTimeImmutable($data['startDate']));
        }
        if (array_key_exists('endDate', $data)) {
            $diploma->setEndDate($data['endDate'] ? new DateTimeImmutable($data['endDate']) : null);
        }

        $this->repo->update($diploma);

        echo json_encode(['message' => 'Diplôme mis à jour']);
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
        echo json_encode(['message' => 'Diplôme supprimé']);
        exit;
    }
}
