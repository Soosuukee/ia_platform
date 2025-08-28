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
        
        $sessionProviderId = $_SESSION['provider_id'] ?? null;

        if ($providerId !== $sessionProviderId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON']);
            exit;
        }

        if (empty($data['startTime']) || empty($data['endTime'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            exit;
        }

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

        http_response_code(201);
        echo json_encode(['message' => 'Slot created', 'id' => $slot->getId()]);
        exit;
    }

    // PUT /slots/{id}
    public function update(int $slotId): void
    {
        
        $sessionProviderId = $_SESSION['provider_id'] ?? null;

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
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON']);
            exit;
        }

        if (empty($data['startTime']) || empty($data['endTime']) || !isset($data['isBooked'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            exit;
        }

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
        echo json_encode(['message' => 'Slot updated']);
        exit;
    }

    // PATCH /slots/{id}
    public function patch(int $slotId): void
    {
        
        $sessionProviderId = $_SESSION['provider_id'] ?? null;

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
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON']);
            exit;
        }

        try {
            if (isset($data['startTime'])) {
                $startTime = new DateTimeImmutable($data['startTime']);
                if (isset($data['endTime'])) {
                    $endTime = new DateTimeImmutable($data['endTime']);
                    if ($startTime >= $endTime) {
                        http_response_code(400);
                        echo json_encode(['error' => 'End time must be after start time']);
                        exit;
                    }
                } elseif ($startTime >= $slot->getEndTime()) {
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
        echo json_encode(['message' => 'Slot updated']);
        exit;
    }

    // DELETE /slots/{id}
    public function destroy(int $slotId): void
    {
        
        $sessionProviderId = $_SESSION['provider_id'] ?? null;

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
        echo json_encode(['message' => 'Slot deleted']);
        exit;
    }
}
