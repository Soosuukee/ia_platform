<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Controller;

use Soosuuke\IaPlatform\Repository\ClientRepository;
use Soosuuke\IaPlatform\Repository\RequestRepository;
use Soosuuke\IaPlatform\Repository\BookingRepository;

class ClientDashboardController
{
    private ClientRepository $clientRepository;
    private RequestRepository $requestRepository;
    private BookingRepository $bookingRepository;

    public function __construct(
        ClientRepository $clientRepository,
        RequestRepository $requestRepository,
        BookingRepository $bookingRepository
    ) {
        $this->clientRepository = $clientRepository;
        $this->requestRepository = $requestRepository;
        $this->bookingRepository = $bookingRepository;
    }

    public function dashboard(int $id): void
    {
        
        $sessionClientId = $_SESSION['client_id'] ?? null;

        if ($sessionClientId !== $id) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $client = $this->clientRepository->findById($id);
        if (!$client) {
            http_response_code(404);
            echo json_encode(['error' => 'Client not found']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (in_array($_SERVER['REQUEST_METHOD'], ['PUT', 'PATCH', 'DELETE']) && json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $requests = $this->requestRepository->findAllByClientId($id, 10);
            $bookings = $this->bookingRepository->findAllByClientId($id, 10);

            echo json_encode([
                'message' => 'Welcome to your client dashboard',
                'client' => [
                    'id' => $client->getId(),
                    'firstName' => $client->getFirstName(),
                    'lastName' => $client->getLastName(),
                    'email' => $client->getEmail(),
                    'country' => $client->getCountry(),
                    'createdAt' => $client->getCreatedAt()->format('Y-m-d H:i:s'),
                    'requests' => array_map(fn($r) => [
                        'id' => $r->getRequestId(),
                        'providerId' => $r->getProviderId(),
                        'title' => $r->getTitle(),
                        'description' => $r->getDescription(),
                        'status' => $r->getStatus(),
                        'createdAt' => $r->getCreatedAt()->format('Y-m-d H:i:s'),
                    ], $requests),
                    'bookings' => array_map(fn($b) => [
                        'id' => $b->getId(),
                        'slotId' => $b->getSlotId(),
                        'status' => $b->getStatus(),
                        'createdAt' => $b->getCreatedAt()->format('Y-m-d H:i:s'),
                    ], $bookings),
                ],
            ]);
            exit;
        } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT' && !empty($data)) {
            if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid email format']);
                exit;
            }
            if (isset($data['password']) && strlen($data['password']) < 8) {
                http_response_code(400);
                echo json_encode(['error' => 'Password must be at least 8 characters']);
                exit;
            }
            if (isset($data['firstName']) && strlen($data['firstName']) > 255) {
                http_response_code(400);
                echo json_encode(['error' => 'First name must be 255 characters or less']);
                exit;
            }
            if (isset($data['lastName']) && strlen($data['lastName']) > 255) {
                http_response_code(400);
                echo json_encode(['error' => 'Last name must be 255 characters or less']);
                exit;
            }
            if (isset($data['country']) && strlen($data['country']) > 100) {
                http_response_code(400);
                echo json_encode(['error' => 'Country must be 100 characters or less']);
                exit;
            }

            if (isset($data['email'])) {
                $client->setEmail($data['email']);
            }
            if (isset($data['password'])) {
                $client->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
            }
            if (isset($data['firstName'])) {
                $client->setFirstName($data['firstName']);
            }
            if (isset($data['lastName'])) {
                $client->setLastName($data['lastName']);
            }
            if (isset($data['country'])) {
                $client->setCountry($data['country']);
            }

            $this->clientRepository->update($client);
            echo json_encode(['message' => 'Client profile updated successfully']);
            exit;
        } elseif ($_SERVER['REQUEST_METHOD'] === 'PATCH' && !empty($data)) {
            if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid email format']);
                exit;
            }
            if (isset($data['password']) && strlen($data['password']) < 8) {
                http_response_code(400);
                echo json_encode(['error' => 'Password must be at least 8 characters']);
                exit;
            }
            if (isset($data['firstName']) && strlen($data['firstName']) > 255) {
                http_response_code(400);
                echo json_encode(['error' => 'First name must be 255 characters or less']);
                exit;
            }
            if (isset($data['lastName']) && strlen($data['lastName']) > 255) {
                http_response_code(400);
                echo json_encode(['error' => 'Last name must be 255 characters or less']);
                exit;
            }
            if (isset($data['country']) && strlen($data['country']) > 100) {
                http_response_code(400);
                echo json_encode(['error' => 'Country must be 100 characters or less']);
                exit;
            }

            if (isset($data['email'])) {
                $client->setEmail($data['email']);
            }
            if (isset($data['password'])) {
                $client->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
            }
            if (isset($data['firstName'])) {
                $client->setFirstName($data['firstName']);
            }
            if (isset($data['lastName'])) {
                $client->setLastName($data['lastName']);
            }
            if (isset($data['country'])) {
                $client->setCountry($data['country']);
            }

            $this->clientRepository->update($client);
            echo json_encode(['message' => 'Client profile updated successfully']);
            exit;
        } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $this->clientRepository->delete($id);
            echo json_encode(['message' => 'Client profile deleted successfully']);
            exit;
        }

        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        exit;
    }
}
