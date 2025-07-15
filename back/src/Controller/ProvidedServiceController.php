<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Controller;

use Soosuuke\IaPlatform\Repository\ProvidedServiceRepository;
use Soosuuke\IaPlatform\Entity\ProvidedService;

class ProvidedServiceController
{
    private ProvidedServiceRepository $serviceRepo;

    public function __construct()
    {
        $this->serviceRepo = new ProvidedServiceRepository();
    }

    // GET /provided-services/{id} (public)
    public function show(int $id): void
    {
        $service = $this->serviceRepo->findById($id);

        if (!$service) {
            http_response_code(404);
            echo json_encode(['error' => 'Service not found']);
            exit;
        }

        echo json_encode([
            'id' => $service->getId(),
            'title' => $service->getTitle(),
            'description' => $service->getDescription(),
            'minPrice' => $service->getMinPrice(),
            'maxPrice' => $service->getMaxPrice(),
            'duration' => $service->getDuration(),
            'providerId' => $service->getProviderId(),
        ]);
        exit;
    }

    // GET /providers/{providerId}/services
    public function listByProvider(int $providerId): void
    {
        $sessionProviderId = $_SESSION['provider_id'] ?? null;

        if ($providerId !== $sessionProviderId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $services = $this->serviceRepo->findByProviderId($providerId);
        $output = [];

        foreach ($services as $service) {
            $output[] = [
                'id' => $service->getId(),
                'title' => $service->getTitle(),
                'description' => $service->getDescription(),
                'minPrice' => $service->getMinPrice(),
                'maxPrice' => $service->getMaxPrice(),
                'duration' => $service->getDuration(),
                'providerId' => $service->getProviderId(),
            ];
        }

        echo json_encode($output);
        exit;
    }

    // POST /providers/{providerId}/services
    public function create(int $providerId): void
    {
        $sessionProviderId = $_SESSION['provider_id'] ?? null;

        if ($providerId !== $sessionProviderId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['title']) || empty($data['description']) || empty($data['duration'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing fields']);
            exit;
        }

        $service = new ProvidedService(
            $data['title'],
            $data['description'],
            $data['minPrice'] ?? null,
            $data['maxPrice'] ?? null,
            $data['duration'],
            $providerId
        );

        $this->serviceRepo->save($service);

        http_response_code(201);
        echo json_encode(['message' => 'Service created', 'id' => $service->getId()]);
        exit;
    }

    // PUT /provided-services/{id}
    public function update(int $id): void
    {
        $sessionProviderId = $_SESSION['provider_id'] ?? null;

        $existing = $this->serviceRepo->findById($id);

        if (!$existing || $existing->getProviderId() !== $sessionProviderId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (
            empty($data['title']) || empty($data['description']) ||
            !isset($data['minPrice']) || !isset($data['maxPrice']) || empty($data['duration'])
        ) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing fields']);
            exit;
        }

        $existing->setTitle($data['title']);
        $existing->setDescription($data['description']);
        $existing->setMinPrice((int) $data['minPrice']);
        $existing->setMaxPrice((int) $data['maxPrice']);
        $existing->setDuration($data['duration']);

        $this->serviceRepo->update($existing);

        echo json_encode(['message' => 'Service updated']);
        exit;
    }

    // PATCH /provided-services/{id}
    public function partialUpdate(int $id): void
    {
        $sessionProviderId = $_SESSION['provider_id'] ?? null;

        $existing = $this->serviceRepo->findById($id);

        if (!$existing || $existing->getProviderId() !== $sessionProviderId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['title'])) {
            $existing->setTitle($data['title']);
        }
        if (isset($data['description'])) {
            $existing->setDescription($data['description']);
        }
        if (array_key_exists('minPrice', $data)) {
            $existing->setMinPrice($data['minPrice']);
        }
        if (array_key_exists('maxPrice', $data)) {
            $existing->setMaxPrice($data['maxPrice']);
        }
        if (isset($data['duration'])) {
            $existing->setDuration($data['duration']);
        }

        $this->serviceRepo->update($existing);

        echo json_encode(['message' => 'Service partially updated']);
        exit;
    }

    // DELETE /provided-services/{id}
    public function delete(int $id): void
    {
        $sessionProviderId = $_SESSION['provider_id'] ?? null;

        $existing = $this->serviceRepo->findById($id);

        if (!$existing || $existing->getProviderId() !== $sessionProviderId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $this->serviceRepo->delete($id);

        echo json_encode(['message' => 'Service deleted']);
        exit;
    }
}
