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
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON']);
            exit;
        }

        if (empty($data['title']) || empty($data['description']) || empty($data['duration'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            exit;
        }

        if (strlen($data['title']) > 255) {
            http_response_code(400);
            echo json_encode(['error' => 'Title must be 255 characters or less']);
            exit;
        }
        if (strlen($data['description']) > 1000) {
            http_response_code(400);
            echo json_encode(['error' => 'Description must be 1000 characters or less']);
            exit;
        }
        if (strlen($data['duration']) > 50) {
            http_response_code(400);
            echo json_encode(['error' => 'Duration must be 50 characters or less']);
            exit;
        }
        if (isset($data['minPrice']) && isset($data['maxPrice'])) {
            if (!is_numeric($data['minPrice']) || !is_numeric($data['maxPrice']) || $data['minPrice'] < 0 || $data['maxPrice'] < $data['minPrice']) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid price range']);
                exit;
            }
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
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON']);
            exit;
        }

        if (
            empty($data['title']) || empty($data['description']) ||
            !isset($data['minPrice']) || !isset($data['maxPrice']) || empty($data['duration'])
        ) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            exit;
        }

        if (strlen($data['title']) > 255) {
            http_response_code(400);
            echo json_encode(['error' => 'Title must be 255 characters or less']);
            exit;
        }
        if (strlen($data['description']) > 1000) {
            http_response_code(400);
            echo json_encode(['error' => 'Description must be 1000 characters or less']);
            exit;
        }
        if (strlen($data['duration']) > 50) {
            http_response_code(400);
            echo json_encode(['error' => 'Duration must be 50 characters or less']);
            exit;
        }
        if (!is_numeric($data['minPrice']) || !is_numeric($data['maxPrice']) || $data['minPrice'] < 0 || $data['maxPrice'] < $data['minPrice']) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid price range']);
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
        if (isset($data['description']) && strlen($data['description']) > 1000) {
            http_response_code(400);
            echo json_encode(['error' => 'Description must be 1000 characters or less']);
            exit;
        }
        if (isset($data['duration']) && strlen($data['duration']) > 50) {
            http_response_code(400);
            echo json_encode(['error' => 'Duration must be 50 characters or less']);
            exit;
        }
        if (isset($data['minPrice']) || isset($data['maxPrice'])) {
            $minPrice = isset($data['minPrice']) ? (int) $data['minPrice'] : $existing->getMinPrice();
            $maxPrice = isset($data['maxPrice']) ? (int) $data['maxPrice'] : $existing->getMaxPrice();
            if ($minPrice < 0 || $maxPrice < $minPrice) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid price range']);
                exit;
            }
        }

        if (isset($data['title'])) {
            $existing->setTitle($data['title']);
        }
        if (isset($data['description'])) {
            $existing->setDescription($data['description']);
        }
        if (isset($data['minPrice'])) {
            $existing->setMinPrice((int) $data['minPrice']);
        }
        if (isset($data['maxPrice'])) {
            $existing->setMaxPrice((int) $data['maxPrice']);
        }
        if (isset($data['duration'])) {
            $existing->setDuration($data['duration']);
        }

        $this->serviceRepo->update($existing);
        echo json_encode(['message' => 'Service updated']);
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
