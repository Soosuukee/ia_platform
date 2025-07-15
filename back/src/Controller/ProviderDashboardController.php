<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Controller;

use Soosuuke\IaPlatform\Repository\ProviderRepository;
use Soosuuke\IaPlatform\Repository\ProviderSkillRepository;
use Soosuuke\IaPlatform\Repository\AvailabilitySlotRepository;
use Soosuuke\IaPlatform\Repository\CompletedWorkRepository;
use Soosuuke\IaPlatform\Repository\ReviewRepository;
use Soosuuke\IaPlatform\Repository\RequestRepository;
use Soosuuke\IaPlatform\Repository\ProviderDiplomaRepository;
use Soosuuke\IaPlatform\Repository\ProvidedServiceRepository;
use PDO;

class ProviderDashboardController
{
    private ProviderRepository $providerRepository;
    private ProviderSkillRepository $skillRepository;
    private AvailabilitySlotRepository $slotRepository;
    private CompletedWorkRepository $workRepository;
    private ReviewRepository $reviewRepository;
    private RequestRepository $requestRepository;
    private ProviderDiplomaRepository $diplomaRepository;
    private ProvidedServiceRepository $serviceRepository;
    private PDO $pdo;

    public function __construct(
        ProviderRepository $providerRepository,
        ProviderSkillRepository $skillRepository,
        AvailabilitySlotRepository $slotRepository,
        CompletedWorkRepository $workRepository,
        ReviewRepository $reviewRepository,
        RequestRepository $requestRepository,
        ProviderDiplomaRepository $diplomaRepository,
        ProvidedServiceRepository $serviceRepository,
        PDO $pdo
    ) {
        $this->providerRepository = $providerRepository;
        $this->skillRepository = $skillRepository;
        $this->slotRepository = $slotRepository;
        $this->workRepository = $workRepository;
        $this->reviewRepository = $reviewRepository;
        $this->requestRepository = $requestRepository;
        $this->diplomaRepository = $diplomaRepository;
        $this->serviceRepository = $serviceRepository;
        $this->pdo = $pdo;
    }

    public function dashboard(int $id): void
    {
        session_start();
        $sessionProviderId = $_SESSION['provider_id'] ?? null;
        session_write_close();

        if ($sessionProviderId !== $id) {
            http_response_code(403);
            echo json_encode(['error' => 'Not logged in']);
            exit;
        }

        if (in_array($_SERVER['REQUEST_METHOD'], ['PUT', 'PATCH', 'DELETE'])) {
            $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            if (!hash_equals($_SESSION['csrf_token'] ?? '', $csrfToken)) {
                http_response_code(403);
                echo json_encode(['error' => 'Invalid CSRF token']);
                exit;
            }
        }

        $provider = $this->providerRepository->findById($id);
        if (!$provider || $provider->getRole() !== 'provider') {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE && in_array($_SERVER['REQUEST_METHOD'], ['PUT', 'PATCH', 'DELETE'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $skills = $this->skillRepository->findAllSkillsByProviderId($id, 10);
            $slots = $this->slotRepository->findAvailableByProviderId($id, 10);
            $works = $this->workRepository->findAllByProviderId($id, 10);
            $reviews = $this->reviewRepository->findAllByProviderId($id, 10);
            $requests = $this->requestRepository->findAllByProviderId($id, 10);
            $diplomas = $this->diplomaRepository->findAllByProviderId($id, 10);
            $services = $this->serviceRepository->findByProviderId($id, 10);

            echo json_encode([
                'message' => 'Welcome to your provider dashboard',
                'provider' => [
                    'id' => $provider->getId(),
                    'firstName' => $provider->getFirstName(),
                    'lastName' => $provider->getLastName(),
                    'email' => $provider->getEmail(),
                    'title' => $provider->getTitle(),
                    'presentation' => $provider->getPresentation(),
                    'country' => $provider->getCountry(),
                    'profilePicture' => $provider->getProfilePicture(),
                    'socialLinks' => $provider->getSocialLinks(), // Ajout des liens sociaux
                    'createdAt' => $provider->getCreatedAt()->format('Y-m-d H:i:s'),
                    'skills' => array_map(fn($s) => [
                        'id' => $s->getId(),
                        'name' => $s->getName(),
                    ], $skills),
                    'availabilitySlots' => array_map(fn($s) => [
                        'id' => $s->getId(),
                        'start' => $s->getStartTime()->format('Y-m-d H:i:s'),
                        'end' => $s->getEndTime()->format('Y-m-d H:i:s'),
                        'isBooked' => $s->isBooked(),
                    ], $slots),
                    'completedWorks' => array_map(fn($w) => [
                        'id' => $w->getId(),
                        'company' => $w->getCompany(),
                        'title' => $w->getTitle(),
                        'description' => $w->getDescription(),
                        'startDate' => $w->getStartDate()->format('Y-m-d'),
                        'endDate' => $w->getEndDate()?->format('Y-m-d'),
                    ], $works),
                    'reviews' => array_map(fn($r) => [
                        'id' => $r->getId(),
                        'rating' => $r->getRating(),
                        'content' => $r->getContent(),
                        'createdAt' => $r->getCreatedAt()->format('Y-m-d H:i:s'),
                    ], $reviews),
                    'requests' => array_map(fn($r) => [
                        'id' => $r->getRequestId(),
                        'title' => $r->getTitle(),
                        'description' => $r->getDescription(),
                        'status' => $r->getStatus(),
                        'createdAt' => $r->getCreatedAt()->format('Y-m-d H:i:s'),
                    ], $requests),
                    'diplomas' => array_map(fn($d) => [
                        'id' => $d->getId(),
                        'title' => $d->getTitle(),
                        'institution' => $d->getInstitution(),
                        'description' => $d->getDescription(),
                        'startDate' => $d->getStartDate()?->format('Y-m-d'),
                        'endDate' => $d->getEndDate()?->format('Y-m-d'),
                    ], $diplomas),
                    'services' => array_map(fn($s) => [
                        'id' => $s->getId(),
                        'title' => $s->getTitle(),
                        'description' => $s->getDescription(),
                        'minPrice' => $s->getMinPrice(),
                        'maxPrice' => $s->getMaxPrice(),
                        'duration' => $s->getDuration(),
                    ], $services),
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

            // Validation des liens sociaux
            if (isset($data['socialLinks']) && is_array($data['socialLinks'])) {
                foreach ($data['socialLinks'] as $link) {
                    if (!empty($link) && !filter_var($link, FILTER_VALIDATE_URL)) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Invalid social link format']);
                        exit;
                    }
                }
            }

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
            if (isset($data['profilePicture'])) {
                $provider->setProfilePicture($data['profilePicture']);
            }
            if (isset($data['socialLinks'])) {
                $provider->setSocialLinks($data['socialLinks']);
            }

            $this->providerRepository->update($provider);
            $this->logAction("Provider {$id} updated their profile");
            echo json_encode(['message' => 'Provider profile updated successfully']);
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

            // Validation des liens sociaux
            if (isset($data['socialLinks']) && is_array($data['socialLinks'])) {
                foreach ($data['socialLinks'] as $link) {
                    if (!empty($link) && !filter_var($link, FILTER_VALIDATE_URL)) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Invalid social link format']);
                        exit;
                    }
                }
            }

            if (isset($data['firstName'])) {
                $provider->setFirstName($data['firstName']);
            }
            if (isset($data['lastName'])) {
                $provider->setLastName($data['lastName']);
            }
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
            if (isset($data['profilePicture'])) {
                $provider->setProfilePicture($data['profilePicture']);
            }
            if (isset($data['socialLinks'])) {
                $provider->setSocialLinks($data['socialLinks']);
            }

            $this->providerRepository->update($provider);
            $this->logAction("Provider {$id} partially updated their profile");
            echo json_encode(['message' => 'Provider profile partially updated']);
            exit;
        } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            if (empty($data['password']) || !password_verify($data['password'], $provider->getPassword())) {
                http_response_code(401);
                echo json_encode(['error' => 'Invalid password for account deletion']);
                exit;
            }

            try {
                $this->pdo->beginTransaction();
                $this->skillRepository->deleteByProviderId($id);
                $this->slotRepository->deleteByProviderId($id);
                $this->workRepository->deleteByProviderId($id);
                $this->reviewRepository->deleteByProviderId($id);
                $this->requestRepository->deleteByProviderId($id);
                $this->diplomaRepository->deleteByProviderId($id);
                $this->serviceRepository->deleteByProviderId($id);
                $this->providerRepository->delete($id);
                $this->pdo->commit();
                $this->logAction("Provider {$id} deleted their account");
                session_destroy();
                echo json_encode(['message' => 'Provider account deleted successfully']);
                exit;
            } catch (\Exception $e) {
                $this->pdo->rollBack();
                http_response_code(500);
                echo json_encode(['error' => 'Failed to delete account: ' . $e->getMessage()]);
                exit;
            }
        }

        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        exit;
    }

    public function publicProfile(int $id): void
    {
        $provider = $this->providerRepository->findById($id);
        if (!$provider) {
            http_response_code(404);
            echo json_encode(['error' => 'Provider not found']);
            exit;
        }

        $skills = $this->skillRepository->findAllSkillsByProviderId($id, 10);
        $services = $this->serviceRepository->findByProviderId($id, 10);
        $works = $this->workRepository->findAllByProviderId($id, 10);
        $diplomas = $this->diplomaRepository->findAllByProviderId($id, 10);

        // Récupération des liens sociaux directement depuis l'entité
        $socialLinks = $provider->getSocialLinks();

        echo json_encode([
            'home' => [
                'id' => $provider->getId(),
                'firstName' => $provider->getFirstName(),
                'lastName' => $provider->getLastName(),
                'bio' => $provider->getPresentation() ?: 'Pas de bio disponible',
            ],
            'about' => [
                'title' => $provider->getTitle() ?: 'Pas de titre',
                'presentation' => $provider->getPresentation() ?: 'Pas de détails',
                'country' => $provider->getCountry() ?: 'Non spécifié',
                'createdAt' => $provider->getCreatedAt()->format('Y-m-d'),
                'diplomas' => array_map(fn($d) => [
                    'id' => $d->getId(),
                    'title' => $d->getTitle(),
                    'institution' => $d->getInstitution(),
                    'description' => $d->getDescription(),
                ], $diplomas),
                'completedWorks' => array_map(fn($w) => [
                    'id' => $w->getId(),
                    'company' => $w->getCompany(),
                    'title' => $w->getTitle(),
                    'description' => $w->getDescription(),
                    'startDate' => $w->getStartDate()->format('Y-m-d'),
                    'endDate' => $w->getEndDate()?->format('Y-m-d'),
                ], $works),
                'socialLinks' => $socialLinks, // Utilisation directe des liens sociaux
            ],
            'services' => array_map(fn($s) => [
                'id' => $s->getId(),
                'title' => $s->getTitle(),
                'description' => $s->getDescription(),
                'minPrice' => $s->getMinPrice(),
                'maxPrice' => $s->getMaxPrice(),
            ], $services),
        ]);
        exit;
    }

    private function logAction(string $message): void
    {
        $logMessage = sprintf("[%s] %s\n", date('Y-m-d H:i:s'), $message);
        file_put_contents(__DIR__ . '/../../logs/provider_actions.log', $logMessage, FILE_APPEND);
    }
}
