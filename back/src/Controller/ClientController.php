<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Controller;

use Soosuuke\IaPlatform\Entity\Client;
use Soosuuke\IaPlatform\Repository\ClientRepository;
use Soosuuke\IaPlatform\Repository\BookingRepository;
use Soosuuke\IaPlatform\Repository\RequestRepository;
use Soosuuke\IaPlatform\Repository\ReviewRepository;

class ClientController
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

    // POST /clients
    public function register(): void
    {
        session_start();

        // CSRF protection
        $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $csrfToken)) {
            http_response_code(403);
            echo json_encode(['error' => 'Invalid CSRF token']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (
            empty($data['firstName']) ||
            empty($data['lastName']) ||
            empty($data['email']) ||
            empty($data['password']) ||
            empty($data['country'])
        ) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            exit;
        }

        // Input validation
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid email format']);
            exit;
        }
        if (strlen($data['password']) < 8) {
            http_response_code(400);
            echo json_encode(['error' => 'Password must be at least 8 characters']);
            exit;
        }

        $existing = $this->clientRepo->findByEmail($data['email']);
        if ($existing) {
            http_response_code(409);
            echo json_encode(['error' => 'Email already in use']);
            exit;
        }

        $client = new Client(
            $data['firstName'],
            $data['lastName'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT),
            $data['country']
        );

        $this->clientRepo->save($client);
        $this->logAction("Client registered with ID {$client->getId()}");

        http_response_code(201);
        echo json_encode(['message' => 'Client registered successfully', 'id' => $client->getId()]);
        exit;
    }

    // POST /clients/login
    public function login(): void
    {
        session_start();

        // CSRF protection
        $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $csrfToken)) {
            http_response_code(403);
            echo json_encode(['error' => 'Invalid CSRF token']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing email or password']);
            exit;
        }

        // Input validation
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid email format']);
            exit;
        }

        $client = $this->clientRepo->findByEmail($data['email']);
        if (!$client || !password_verify($data['password'], $client->getPassword())) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
            exit;
        }

        $_SESSION['client_id'] = $client->getId();
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Generate new CSRF token

        $this->logAction("Client {$client->getId()} logged in");

        echo json_encode(['message' => 'Login successful', 'csrf_token' => $_SESSION['csrf_token']]);
        exit;
    }

    // POST /clients/logout
    public function logout(): void
    {
        session_start();

        // CSRF protection
        $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $csrfToken)) {
            http_response_code(403);
            echo json_encode(['error' => 'Invalid CSRF token']);
            exit;
        }

        $clientId = $_SESSION['client_id'] ?? 'unknown';
        session_destroy();
        $this->logAction("Client {$clientId} logged out");

        echo json_encode(['message' => 'Logout successful']);
        exit;
    }

    // GET /clients/{id}
    public function show(int $id): void
    {
        session_start();
        if (!isset($_SESSION['client_id']) || $_SESSION['client_id'] !== $id) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $client = $this->clientRepo->findById($id);
        if (!$client || $client->getRole() !== 'client') {
            http_response_code(404);
            echo json_encode(['error' => 'Client not found']);
            exit;
        }

        echo json_encode([
            'id' => $client->getId(),
            'firstName' => $client->getFirstName(),
            'lastName' => $client->getLastName(),
            'email' => $client->getEmail(),
            'country' => $client->getCountry(),
            'createdAt' => $client->getCreatedAt()->format('Y-m-d H:i:s')
        ]);
        exit;
    }

    // DELETE /clients/{id}
    public function destroy(int $id): void
    {
        session_start();
        if (!isset($_SESSION['client_id']) || $_SESSION['client_id'] !== $id) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        // CSRF protection
        $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $csrfToken)) {
            http_response_code(403);
            echo json_encode(['error' => 'Invalid CSRF token']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        $client = $this->clientRepo->findById($id);
        if (!$client || $client->getRole() !== 'client') {
            http_response_code(404);
            echo json_encode(['error' => 'Client not found']);
            exit;
        }

        // Password confirmation for deletion
        if (empty($data['password']) || !password_verify($data['password'], $client->getPassword())) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid password for account deletion']);
            exit;
        }

        // Cascade deletion
        $this->bookingRepo->deleteByClientId($id);
        $this->requestRepo->deleteByClientId($id);
        $this->reviewRepo->deleteByClientId($id);
        $this->clientRepo->deleteByClientId($id);

        $this->logAction("Client {$id} deleted their account");
        session_destroy();

        echo json_encode(['message' => 'Client account deleted successfully']);
        exit;
    }

    private function logAction(string $message): void
    {
        $logMessage = sprintf("[%s] %s\n", date('Y-m-d H:i:s'), $message);
        file_put_contents(__DIR__ . '/../../logs/client_actions.log', $logMessage, FILE_APPEND);
    }
}
