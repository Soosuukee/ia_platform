<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Controller;

use Soosuuke\IaPlatform\Entity\Client;
use Soosuuke\IaPlatform\Repository\ClientRepository;

class ClientController
{
    private ClientRepository $clientRepo;

    public function __construct()
    {
        $this->clientRepo = new ClientRepository();
    }

    public function register(array $data): void
    {
        if (
            empty($data['firstName']) ||
            empty($data['lastName']) ||
            empty($data['email']) ||
            empty($data['password']) ||
            empty($data['country'])
        ) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            return;
        }

        $existing = $this->clientRepo->findByEmail($data['email']);
        if ($existing) {
            http_response_code(409);
            echo json_encode(['error' => 'Email already in use']);
            return;
        }

        $client = new Client(
            $data['firstName'],
            $data['lastName'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['country']
        );

        $this->clientRepo->save($client);

        http_response_code(201);
        echo json_encode(['message' => 'Client registered successfully']);
    }

    public function login(array $data): void
    {
        if (empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing email or password']);
            return;
        }

        $client = $this->clientRepo->findByEmail($data['email']);
        if (!$client || !password_verify($data['password'], $client->getPassword())) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
            return;
        }

        $_SESSION['client_id'] = $client->getId();

        echo json_encode(['message' => 'Login successful']);
    }

    public function logout(): void
    {
        unset($_SESSION['client_id']);
        echo json_encode(['message' => 'Logout successful']);
    }

    public function dashboard(): void
    {
        if (!isset($_SESSION['client_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        echo json_encode(['message' => 'Welcome to the client dashboard']);
    }
}
