<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Controller;

use Soosuuke\IaPlatform\Repository\RequestRepository;
use Soosuuke\IaPlatform\Entity\Request;

class RequestController
{
    private RequestRepository $repo;

    public function __construct()
    {
        $this->repo = new RequestRepository();
    }

    // POST /requests
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

        if (empty($data['providerId']) || empty($data['title']) || empty($data['description'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            exit;
        }

        if (!is_numeric($data['providerId']) || $data['providerId'] <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid providerId']);
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

        $request = new Request(
            (int) $clientId,
            (int) $data['providerId'],
            $data['title'],
            $data['description']
        );

        $this->repo->save($request);

        http_response_code(201);
        echo json_encode(['message' => 'Request created', 'id' => $request->getRequestId()]);
        exit;
    }

    // GET /requests/provider/{providerId}
    public function getByProvider(int $providerId): void
    {
        
        $sessionProviderId = $_SESSION['provider_id'] ?? null;

        if ((int) $providerId !== $sessionProviderId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $requests = $this->repo->findAllByProviderId($providerId);
        $data = array_map(fn($r) => $this->serialize($r), $requests);

        echo json_encode($data);
        exit;
    }

    // GET /requests/client/{clientId}
    public function getByClient(int $clientId): void
    {
        
        $sessionClientId = $_SESSION['client_id'] ?? null;

        if ((int) $clientId !== $sessionClientId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $requests = $this->repo->findAllByClientId($clientId);
        $data = array_map(fn($r) => $this->serialize($r), $requests);

        echo json_encode($data);
        exit;
    }

    // PATCH /requests/{id}
    public function updateStatus(int $id): void
    {
        
        $sessionProviderId = $_SESSION['provider_id'] ?? null;

        $data = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON']);
            exit;
        }

        if (empty($data['status'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing status']);
            exit;
        }

        $validStatuses = ['pending', 'accepted', 'declined', 'completed'];
        if (!in_array($data['status'], $validStatuses, true)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid status']);
            exit;
        }

        $request = $this->repo->findById($id);
        if (!$request || $request->getProviderId() !== $sessionProviderId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized or request not found']);
            exit;
        }

        $this->repo->updateStatus($id, $data['status']);
        echo json_encode(['message' => 'Status updated']);
        exit;
    }

    // DELETE /requests/{id}
    public function destroy(int $id): void
    {
        
        $sessionClientId = $_SESSION['client_id'] ?? null;

        $request = $this->repo->findById($id);
        if (!$request || $request->getClientId() !== $sessionClientId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized or request not found']);
            exit;
        }

        $this->repo->delete($id);
        echo json_encode(['message' => 'Request deleted']);
        exit;
    }

    // Private helper
    private function serialize(Request $r): array
    {
        return [
            'id' => $r->getRequestId(),
            'clientId' => $r->getClientId(),
            'providerId' => $r->getProviderId(),
            'title' => $r->getTitle(),
            'description' => $r->getDescription(),
            'status' => $r->getStatus(),
            'createdAt' => $r->getCreatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
