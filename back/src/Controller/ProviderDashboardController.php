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

        $sessionProviderId = $_SESSION['provider_id'] ?? null;

        // Log pour debug
        error_log("Dashboard access attempt: requested_id=$id, session_provider_id=$sessionProviderId, session_id=" . session_id());
        error_log("Session data: " . print_r($_SESSION, true));

        if ($sessionProviderId !== $id) {
            error_log("Authentication failed: session provider ID ($sessionProviderId) does not match requested ID ($id)");
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized - Session mismatch']);
            exit;
        }

        $provider = $this->providerRepository->findById($id);
        if (!$provider || $provider->getRole() !== 'provider') {
            error_log("Provider not found or wrong role: provider_id=$id");
            http_response_code(403);
            echo json_encode(['error' => 'Access denied - Provider not found or wrong role']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (in_array($_SERVER['REQUEST_METHOD'], ['PUT', 'PATCH', 'DELETE']) && json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            try {
                $skills = $this->skillRepository->findAllSkillsByProviderId($id, 10);
                $slots = $this->slotRepository->findAvailableByProviderId($id, 10);
                $works = $this->workRepository->findAllByProviderId($id, 10);
                $reviews = $this->reviewRepository->findAllByProviderId($id, 10);
                $requests = $this->requestRepository->findAllByProviderId($id, 10);
                $diplomas = $this->diplomaRepository->findAllByProviderId($id, 10);
                $services = $this->serviceRepository->findByProviderId($id, 10);

                error_log("Dashboard data loaded successfully for provider $id");

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
                        'socialLinks' => $provider->getSocialLinks(),
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
                            'conmment' => $r->getComment(),
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
            } catch (\Exception $e) {
                error_log("Dashboard data loading error: " . $e->getMessage());
                http_response_code(500);
                echo json_encode(['error' => 'Failed to load dashboard data: ' . $e->getMessage()]);
                exit;
            }
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
            if (isset($data['socialLinks']) && is_array($data['socialLinks'])) {
                foreach ($data['socialLinks'] as $link) {
                    if (!empty($link) && !filter_var($link, FILTER_VALIDATE_URL)) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Invalid social link format']);
                        exit;
                    }
                }
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
            if (isset($data['title']) && strlen($data['title']) > 255) {
                http_response_code(400);
                echo json_encode(['error' => 'Title must be 255 characters or less']);
                exit;
            }
            if (isset($data['presentation']) && strlen($data['presentation']) > 1000) {
                http_response_code(400);
                echo json_encode(['error' => 'Presentation must be 1000 characters or less']);
                exit;
            }
            if (isset($data['country']) && strlen($data['country']) > 100) {
                http_response_code(400);
                echo json_encode(['error' => 'Country must be 100 characters or less']);
                exit;
            }
            if (isset($data['profilePicture']) && !empty($data['profilePicture']) && !filter_var($data['profilePicture'], FILTER_VALIDATE_URL)) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid profile picture URL']);
                exit;
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
            if (isset($data['socialLinks']) && is_array($data['socialLinks'])) {
                foreach ($data['socialLinks'] as $link) {
                    if (!empty($link) && !filter_var($link, FILTER_VALIDATE_URL)) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Invalid social link format']);
                        exit;
                    }
                }
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
            if (isset($data['title']) && strlen($data['title']) > 255) {
                http_response_code(400);
                echo json_encode(['error' => 'Title must be 255 characters or less']);
                exit;
            }
            if (isset($data['presentation']) && strlen($data['presentation']) > 1000) {
                http_response_code(400);
                echo json_encode(['error' => 'Presentation must be 1000 characters or less']);
                exit;
            }
            if (isset($data['country']) && strlen($data['country']) > 100) {
                http_response_code(400);
                echo json_encode(['error' => 'Country must be 100 characters or less']);
                exit;
            }
            if (isset($data['profilePicture']) && !empty($data['profilePicture']) && !filter_var($data['profilePicture'], FILTER_VALIDATE_URL)) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid profile picture URL']);
                exit;
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
            echo json_encode(['message' => 'Provider profile updated successfully']);
            exit;
        } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $this->providerRepository->delete($id);
            echo json_encode(['message' => 'Provider profile deleted successfully']);
            exit;
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
        $works = $this->workRepository->findAllByProviderId($id, 10);
        $reviews = $this->reviewRepository->findAllByProviderId($id, 10);
        $diplomas = $this->diplomaRepository->findAllByProviderId($id, 10);
        $services = $this->serviceRepository->findByProviderId($id, 10);

        // Récupérer les médias pour chaque completed work
        $completedWorksWithMedia = array_map(function ($work) {
            $media = $this->pdo->prepare('SELECT id, media_type, media_url FROM completed_work_media WHERE work_id = ?');
            $media->execute([$work->getId()]);
            $mediaList = $media->fetchAll(\PDO::FETCH_ASSOC);

            return [
                'id' => $work->getId(),
                'company' => $work->getCompany(),
                'title' => $work->getTitle(),
                'description' => $work->getDescription(),
                'startDate' => $work->getStartDate()->format('Y-m-d'),
                'endDate' => $work->getEndDate()?->format('Y-m-d'),
                'media' => array_map(fn($m) => [
                    'id' => $m['id'],
                    'filename' => $m['media_url']
                ], $mediaList)
            ];
        }, $works);

        echo json_encode([
            'provider' => [
                'id' => $provider->getId(),
                'firstName' => $provider->getFirstName(),
                'lastName' => $provider->getLastName(),
                'title' => $provider->getTitle(),
                'presentation' => $provider->getPresentation(),
                'country' => $provider->getCountry(),
                'profilePicture' => $provider->getProfilePicture(),
                'socialLinks' => $provider->getSocialLinks(),
                'joinedAt' => $provider->getCreatedAt()->format('Y-m-d H:i:s'),
                'skills' => array_map(fn($s) => [
                    'id' => $s->getId(),
                    'name' => $s->getName(),
                ], $skills),
                'completedWorks' => $completedWorksWithMedia,
                'reviews' => array_map(fn($r) => [
                    'id' => $r->getId(),
                    'rating' => $r->getRating(),
                    'comment' => $r->getComment(),
                    'createdAt' => $r->getCreatedAt()->format('Y-m-d H:i:s'),
                ], $reviews),
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
    }
}
