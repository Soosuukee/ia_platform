<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Controller;

use Soosuuke\IaPlatform\Repository\ProviderRepository;
use Soosuuke\IaPlatform\Entity\Provider;

class ProviderController
{
    private ProviderRepository $providerRepository;

    public function __construct()
    {
        $this->providerRepository = new ProviderRepository();
    }

    // GET /providers/{id}
    public function show(int $id): void
    {
        $provider = $this->providerRepository->findById($id);

        if (!$provider) {
            http_response_code(404);
            echo json_encode(['error' => 'Provider not found']);
            return;
        }

        echo json_encode([
            'id' => $provider->getId(),
            'firstName' => $provider->getFirstName(),
            'lastName' => $provider->getLastName(),
            'email' => $provider->getEmail(),
            'title' => $provider->getTitle(),
            'presentation' => $provider->getPresentation(),
            'country' => $provider->getCountry(),
            'createdAt' => $provider->getCreatedAt()->format('Y-m-d H:i:s'),
        ]);
    }


    // POST /providers (Register)
    public function register(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (
            empty($data['firstName']) || empty($data['lastName']) ||
            empty($data['email']) || empty($data['password']) ||
            empty($data['title']) || empty($data['presentation']) || empty($data['country'])
        ) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing fields']);
            return;
        }

        if ($this->providerRepository->findByEmail($data['email'])) {
            http_response_code(409);
            echo json_encode(['error' => 'Email already in use']);
            return;
        }

        $provider = new Provider(
            $data['firstName'],
            $data['lastName'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT),
            $data['title'],
            $data['presentation'],
            $data['country']
        );

        $this->providerRepository->save($provider);

        http_response_code(201);
        echo json_encode(['message' => 'Provider registered', 'id' => $provider->getId()]);
    }

    // POST /providers/login
    public function login(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing credentials']);
            return;
        }

        $provider = $this->providerRepository->findByEmail($data['email']);

        if (!$provider || !password_verify($data['password'], $provider->getPassword())) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
            return;
        }

        session_start();
        $_SESSION['provider_id'] = $provider->getId();

        echo json_encode(['message' => 'Login successful']);
    }

    // POST /providers/logout
    public function logout(): void
    {
        session_start();
        session_destroy();
        echo json_encode(['message' => 'Logout successful']);
    }

    // PUT /providers/{id}
    public function update(int $id): void
    {
        $provider = $this->providerRepository->findById($id);

        if (!$provider) {
            http_response_code(404);
            echo json_encode(['error' => 'Provider not found']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['email'])) {
            $provider->setEmail($data['email']);
        }
        if (isset($data['password'])) {
            $provider->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
        }
        if (isset($data['title'])) {
            $provider->setTitle($data['title']);
        }
        if (isset($data['presentation'])) {
            $provider->setPresentation($data['presentation']);
        }
        if (isset($data['country'])) {
            $provider->setCountry($data['country']);
        }

        // À toi de faire une méthode update() dans le repo si tu veux persister ces changements

        echo json_encode(['message' => 'Provider updated']);
    }

    // DELETE /providers/{id}
    public function destroy(int $id): void
    {
        $provider = $this->providerRepository->findById($id);

        if (!$provider) {
            http_response_code(404);
            echo json_encode(['error' => 'Provider not found']);
            return;
        }

        $this->providerRepository->delete($id);

        echo json_encode(['message' => 'Provider deleted']);
    }

    // GET /providers/dashboard
    public function dashboard(): void
    {
        session_start();

        if (!isset($_SESSION['provider_id'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Not logged in']);
            return;
        }

        $provider = $this->providerRepository->findById((int) $_SESSION['provider_id']);

        if (!$provider) {
            http_response_code(404);
            echo json_encode(['error' => 'Provider not found']);
            return;
        }

        echo json_encode([
            'message' => 'Welcome to your dashboard',
            'provider' => [
                'id' => $provider->getId(),
                'firstName' => $provider->getFirstName(),
                'lastName' => $provider->getLastName(),
                'email' => $provider->getEmail(),
                'title' => $provider->getTitle(),
                'presentation' => $provider->getPresentation(),
                'country' => $provider->getCountry(),
            ],
        ]);
    }
}
