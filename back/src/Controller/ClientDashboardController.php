<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Controller;

use Soosuuke\IaPlatform\Repository\ClientRepository;
use Soosuuke\IaPlatform\Repository\BookingRepository;
use Soosuuke\IaPlatform\Repository\RequestRepository;
use Soosuuke\IaPlatform\Repository\ReviewRepository;

class ClientDashboardController
{
    private ClientRepository $clientRepo;
    private BookingRepository $bookingRepo;
    private RequestRepository $requestRepo;
    private ReviewRepository $reviewRepo;

    public function __construct(
        ClientRepository $clientRepo,
        BookingRepository $bookingRepo,
        RequestRepository $requestRepo,
        ReviewRepository $reviewRepo
    ) {
        $this->clientRepo = $clientRepo;
        $this->bookingRepo = $bookingRepo;
        $this->requestRepo = $requestRepo;
        $this->reviewRepo = $reviewRepo;
    }

    public function dashboard(int $id): void
    {
        if (!isset($_SESSION['client_id']) || $_SESSION['client_id'] !== $id) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $method = $_SERVER['REQUEST_METHOD'];
        if (in_array($method, ['PUT', 'PATCH', 'DELETE'])) {
            $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            if (!hash_equals($_SESSION['csrf_token'] ?? '', $csrfToken)) {
                http_response_code(403);
                echo json_encode(['error' => 'Invalid CSRF token']);
                exit;
            }
        }

        $client = $this->clientRepo->findById($id);
        if (!$client || $client->getRole() !== 'client') {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            exit;
        }

        if ($method === 'GET') {
            $bookings = $this->bookingRepo->findAllByClientId($id, 10);
            $requests = $this->requestRepo->findAllByClientId($id, 10);
            $reviews = $this->reviewRepo->findAllByClientId($id, 10);

            echo json_encode([
                'message' => 'Welcome to the client dashboard',
                'client' => [
                    'id' => $client->getId(),
                    'firstName' => $client->getFirstName(),
                    'lastName' => $client->getLastName(),
                    'email' => $client->getEmail(),
                    'country' => $client->getCountry(),
                    'createdAt' => $client->getCreatedAt()->format('Y-m-d H:i:s')
                ],
                'bookings' => array_map(fn($b) => [
                    'id' => $b->getId(),
                    'status' => $b->getStatus(),
                    'slotId' => $b->getSlotId(),
                    'createdAt' => $b->getCreatedAt()->format('Y-m-d H:i:s')
                ], $bookings),
                'requests' => array_map(fn($r) => [
                    'id' => $r->getId(),
                    'status' => $r->getStatus(),
                    'createdAt' => $r->getCreatedAt()->format('Y-m-d H:i:s')
                ], $requests),
                'reviews' => array_map(fn($r) => [
                    'id' => $r->getId(),
                    'rating' => $r->getRating(),
                    'comment' => $r->getContent(),
                    'createdAt' => $r->getCreatedAt()->format('Y-m-d H:i:s')
                ], $reviews)
            ]);
            exit;
        } elseif ($method === 'PUT' || $method === 'PATCH') {
            $data = json_decode(file_get_contents('php://input'), true) ?? [];
            if (empty($data)) {
                http_response_code(400);
                echo json_encode(['error' => 'No data provided']);
                exit;
            }

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

            if (isset($data['firstName'])) $client->setFirstName($data['firstName']);
            if (isset($data['lastName'])) $client->setLastName($data['lastName']);
            if (isset($data['email'])) $client->setEmail($data['email']);
            if (isset($data['password'])) $client->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
            if (isset($data['country'])) $client->setCountry($data['country']);

            $this->clientRepo->update($client);
            $this->logAction("Client {$id} " . ($method === 'PUT' ? 'updated' : 'partially updated') . " their profile");
            echo json_encode(['message' => 'Client profile updated']);
            exit;
        } elseif ($method === 'DELETE') {
            $data = json_decode(file_get_contents('php://input'), true) ?? [];
            if (empty($data['password']) || !password_verify($data['password'], $client->getPassword())) {
                http_response_code(401);
                echo json_encode(['error' => 'Invalid password for account deletion']);
                exit;
            }

            $this->bookingRepo->deleteByClientId($id);
            $this->requestRepo->deleteByClientId($id);
            $this->reviewRepo->deleteByClientId($id);
            $this->clientRepo->deleteByClientId($id);
            $this->logAction("Client {$id} deleted their account");
            session_destroy();
            echo json_encode(['message' => 'Client account deleted']);
            exit;
        }

        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        exit;
    }

    private function logAction(string $message): void
    {
        $logMessage = sprintf("[%s] %s\n", date('Y-m-d H:i:s'), $message);
        file_put_contents(__DIR__ . '/../../logs/client_actions.log', $logMessage, FILE_APPEND);
    }
}
