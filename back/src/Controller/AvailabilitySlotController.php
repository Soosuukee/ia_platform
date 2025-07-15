<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Controller;

use Soosuuke\IaPlatform\Repository\AvailabilitySlotRepository;
use Soosuuke\IaPlatform\Repository\ProviderRepository;
use Soosuuke\IaPlatform\Entity\AvailabilitySlot;
use DateTimeImmutable;

class AvailabilitySlotController
{
    private AvailabilitySlotRepository $slotRepo;
    private ProviderRepository $providerRepo;

    public function __construct(
        AvailabilitySlotRepository $slotRepo,
        ProviderRepository $providerRepo
    ) {
        $this->slotRepo = $slotRepo;
        $this->providerRepo = $providerRepo;
    }

    // GET /providers/{providerId}/slots
    public function index(int $providerId): void
    {
        session_start();
        $sessionProviderId = $_SESSION['provider_id'] ?? null;
        if ($providerId !== $sessionProviderId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $slots = $this->slotRepo->findAllByProviderId($providerId);
        $result = [];

        foreach ($slots as $slot) {
            $result[] = [
                'id' => $slot->getId(),
                'startTime' => $slot->getStartTime()->format('Y-m-d H:i:s'),
                'endTime' => $slot->getEndTime()->format('Y-m-d H:i:s'),
                'isBooked' => $slot->isBooked(),
            ];
        }

        echo json_encode($result);
        exit;
    }

    // POST /providers/{providerId}/slots
    public function store(int $providerId): void
    {
        session_start();
        $sessionProviderId = $_SESSION['provider_id'] ?? null;
        if ($providerId !== $sessionProviderId) {
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

        if (empty($data['startTime']) || empty($data['endTime'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing fields']);
            exit;
        }

        // Validate DateTime inputs
        try {
            $startTime = new DateTimeImmutable($data['startTime']);
            $endTime = new DateTimeImmutable($data['endTime']);
            if ($startTime >= $endTime) {
                http_response_code(400);
                echo json_encode(['error' => 'End time must be after start time']);
                exit;
            }
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid date format']);
            exit;
        }

        $provider = $this->providerRepo->findById($providerId);
        if (!$provider) {
            http_response_code(404);
            echo json_encode(['error' => 'Provider not found']);
            exit;
        }

        $slot = new AvailabilitySlot($providerId, $startTime, $endTime, false);

        $this->slotRepo->saveForProvider($providerId, $slot);
        $this->logAction("Provider {$providerId} created availability slot {$slot->getId()}");

        http_response_code(201);
        echo json_encode(['message' => 'Slot created', 'id' => $slot->getId()]);
        exit;
    }

    // PUT /slots/{id}
    public function update(int $slotId): void
    {
        session_start();
        $sessionProviderId = $_SESSION['provider_id'] ?? null;

        // CSRF protection
        $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $csrfToken)) {
            http_response_code(403);
            echo json_encode(['error' => 'Invalid CSRF token']);
            exit;
        }

        $slot = $this->slotRepo->findById($slotId);
        if (!$slot) {
            http_response_code(404);
            echo json_encode(['error' => 'Slot not found']);
            exit;
        }

        if ($slot->getProviderId() !== $sessionProviderId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['startTime']) || empty($data['endTime']) || !isset($data['isBooked'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing fields']);
            exit;
        }

        // Validate DateTime inputs
        try {
            $startTime = new DateTimeImmutable($data['startTime']);
            $endTime = new DateTimeImmutable($data['endTime']);
            if ($startTime >= $endTime) {
                http_response_code(400);
                echo json_encode(['error' => 'End time must be after start time']);
                exit;
            }
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid date format']);
            exit;
        }

        $slot->setStartTime($startTime);
        $slot->setEndTime($endTime);
        $slot->setIsBooked((bool) $data['isBooked']);

        $this->slotRepo->update($slot);
        $this->logAction("Provider {$sessionProviderId} updated availability slot {$slotId}");

        echo json_encode(['message' => 'Slot updated']);
        exit;
    }

    // PATCH /slots/{id}
    public function patch(int $slotId): void
    {
        session_start();
        $sessionProviderId = $_SESSION['provider_id'] ?? null;

        // CSRF protection
        $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $csrfToken)) {
            http_response_code(403);
            echo json_encode(['error' => 'Invalid CSRF token']);
            exit;
        }

        $slot = $this->slotRepo->findById($slotId);
        if (!$slot) {
            http_response_code(404);
            echo json_encode(['error' => 'Slot not found']);
            exit;
        }

        if ($slot->getProviderId() !== $sessionProviderId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        try {
            if (isset($data['startTime'])) {
                $startTime = new DateTimeImmutable($data['startTime']);
                if (isset($data['endTime']) && $startTime >= new DateTimeImmutable($data['endTime'])) {
                    http_response_code(400);
                    echo json_encode(['error' => 'End time must be after start time']);
                    exit;
                }
                $slot->setStartTime($startTime);
            }
            if (isset($data['endTime'])) {
                $endTime = new DateTimeImmutable($data['endTime']);
                if ($slot->getStartTime() >= $endTime) {
                    http_response_code(400);
                    echo json_encode(['error' => 'End time must be after start time']);
                    exit;
                }
                $slot->setEndTime($endTime);
            }
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid date format']);
            exit;
        }

        if (isset($data['isBooked'])) {
            $slot->setIsBooked((bool) $data['isBooked']);
        }

        $this->slotRepo->update($slot);
        $this->logAction("Provider {$sessionProviderId} patched availability slot {$slotId}");

        echo json_encode(['message' => 'Slot patched']);
        exit;
    }

    // DELETE /slots/{id}
    public function destroy(int $slotId): void
    {
        session_start();
        $sessionProviderId = $_SESSION['provider_id'] ?? null;

        // CSRF protection
        $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $csrfToken)) {
            http_response_code(403);
            echo json_encode(['error' => 'Invalid CSRF token']);
            exit;
        }

        $slot = $this->slotRepo->findById($slotId);
        if (!$slot) {
            http_response_code(404);
            echo json_encode(['error' => 'Slot not found']);
            exit;
        }

        if ($slot->getProviderId() !== $sessionProviderId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $this->slotRepo->delete($slotId);
        $this->logAction("Provider {$sessionProviderId} deleted availability slot {$slotId}");

        echo json_encode(['message' => 'Slot deleted']);
        exit;
    }

    private function logAction(string $message): void
    {
        $logMessage = sprintf("[%s] %s\n", date('Y-m-d H:i:s'), $message);
        file_put_contents(__DIR__ . '/../../logs/provider_actions.log', $logMessage, FILE_APPEND);
    }
}
