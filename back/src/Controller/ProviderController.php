<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Controller;

use Soosuuke\IaPlatform\Repository\ProviderRepository;
use Soosuuke\IaPlatform\Repository\ProviderSkillRepository;
use Soosuuke\IaPlatform\Repository\AvailabilitySlotRepository;
use Soosuuke\IaPlatform\Entity\Provider;

class ProviderController
{
    private ProviderRepository $providerRepository;
    private ProviderSkillRepository $skillRepository;
    private AvailabilitySlotRepository $slotRepository;

    public function __construct(
        ProviderRepository $providerRepository,
        ProviderSkillRepository $skillRepository,
        AvailabilitySlotRepository $slotRepository
    ) {
        $this->providerRepository = $providerRepository;
        $this->skillRepository = $skillRepository;
        $this->slotRepository = $slotRepository;
    }

    // GET /providers/{id}
    public function show(int $id): void
    {
        $provider = $this->providerRepository->findById($id);

        if (!$provider) {
            http_response_code(404);
            echo json_encode(['error' => 'Provider not found']);
            exit;
        }

        $skills = $this->skillRepository->findAllSkillsByProviderId($id);
        $slots = $this->slotRepository->findAvailableByProviderId($id);

        $provider->setSkills($skills);
        $provider->setAvailabilitySlots($slots);

        echo json_encode([
            'id' => $provider->getId(),
            'firstName' => $provider->getFirstName(),
            'lastName' => $provider->getLastName(),
            'email' => $provider->getEmail(),
            'title' => $provider->getTitle(),
            'presentation' => $provider->getPresentation(),
            'country' => $provider->getCountry(),
            'createdAt' => $provider->getCreatedAt()->format('Y-m-d H:i:s'),
            'profilePicture' => $provider->getProfilePicture(),
            'socialLinks' => $provider->getSocialLinks(), // Ajout des liens sociaux
            'skills' => array_map(fn($s) => [
                'id' => $s->getId(),
                'name' => $s->getName(),
            ], $skills),
            'availabilitySlots' => array_map(fn($s) => [
                'id' => $s->getId(),
                'start' => $s->getStartTime()->format('Y-m-d H:i:s'),
                'end' => $s->getEndTime()->format('Y-m-d H:i:s'),
                'isBooked' => $s->isBooked(),
            ], $slots)
        ]);
        exit;
    }

    // POST /providers (Register)
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
            empty($data['firstName']) || empty($data['lastName']) ||
            empty($data['email']) || empty($data['password']) ||
            empty($data['title']) || empty($data['presentation']) || empty($data['country'])
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

        // Validation des liens sociaux si fournis
        if (isset($data['socialLinks']) && is_array($data['socialLinks'])) {
            foreach ($data['socialLinks'] as $link) {
                if (!empty($link) && !filter_var($link, FILTER_VALIDATE_URL)) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Invalid social link format']);
                    exit;
                }
            }
        }

        if ($this->providerRepository->findByEmail($data['email'])) {
            http_response_code(409);
            echo json_encode(['error' => 'Email already in use']);
            exit;
        }

        $provider = new Provider(
            $data['firstName'],
            $data['lastName'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT),
            $data['title'],
            $data['presentation'],
            $data['country'],
            $data['profilePicture'] ?? null,
            'provider', // role par défaut
            $data['socialLinks'] ?? [] // liens sociaux
        );

        $this->providerRepository->save($provider);
        $this->logAction("Provider registered with ID {$provider->getId()}");

        http_response_code(201);
        echo json_encode(['message' => 'Provider registered', 'id' => $provider->getId()]);
        exit;
    }

    // POST /providers/login
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
            echo json_encode(['error' => 'Missing credentials']);
            exit;
        }

        // Input validation
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid email format']);
            exit;
        }

        $provider = $this->providerRepository->findByEmail($data['email']);

        if (!$provider || !password_verify($data['password'], $provider->getPassword())) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
            exit;
        }

        $_SESSION['provider_id'] = $provider->getId();
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Generate new CSRF token

        $this->logAction("Provider {$provider->getId()} logged in");

        echo json_encode(['message' => 'Login successful', 'csrf_token' => $_SESSION['csrf_token']]);
        exit;
    }

    // POST /providers/logout
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

        $providerId = $_SESSION['provider_id'] ?? 'unknown';
        session_destroy();
        $this->logAction("Provider {$providerId} logged out");

        echo json_encode(['message' => 'Logout successful']);
        exit;
    }

    // DELETE /providers/{id}
    public function destroy(int $id): void
    {
        session_start();
        $sessionProviderId = $_SESSION['provider_id'] ?? null;
        if ($id !== $sessionProviderId) {
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

        // Password confirmation for deletion
        $provider = $this->providerRepository->findById($id);
        if (!$provider) {
            http_response_code(404);
            echo json_encode(['error' => 'Provider not found']);
            exit;
        }

        if (empty($data['password']) || !password_verify($data['password'], $provider->getPassword())) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid password for account deletion']);
            exit;
        }

        // Cascade deletion
        $this->skillRepository->deleteByProviderId($id);
        $this->slotRepository->deleteByProviderId($id);
        $this->providerRepository->delete($id);

        $this->logAction("Provider {$id} deleted their account");
        session_destroy();

        echo json_encode(['message' => 'Provider account deleted successfully']);
        exit;
    }

    private function logAction(string $message): void
    {
        $logMessage = sprintf("[%s] %s\n", date('Y-m-d H:i:s'), $message);
        file_put_contents(__DIR__ . '/../../logs/provider_actions.log', $logMessage, FILE_APPEND);
    }
}
