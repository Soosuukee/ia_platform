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

    public function __construct()
    {
        $this->slotRepo = new AvailabilitySlotRepository();
        $this->providerRepo = new ProviderRepository();
    }

    // GET /providers/{providerId}/slots
    public function index(int $providerId): void
    {
        $sessionProviderId = $_SESSION['provider_id'] ?? null;
        if ($providerId !== $sessionProviderId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            return;
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
    }

    // POST /providers/{providerId}/slots
    public function store(int $providerId): void
    {
        $sessionProviderId = $_SESSION['provider_id'] ?? null;
        if ($providerId !== $sessionProviderId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['startTime']) || empty($data['endTime'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing fields']);
            return;
        }

        $provider = $this->providerRepo->findById($providerId);

        if (!$provider) {
            http_response_code(404);
            echo json_encode(['error' => 'Provider not found']);
            return;
        }

        $slot = new AvailabilitySlot(
            $providerId,
            new DateTimeImmutable($data['startTime']),
            new DateTimeImmutable($data['endTime']),
            false
        );

        $this->slotRepo->saveForProvider($providerId, $slot);

        http_response_code(201);
        echo json_encode(['message' => 'Slot created']);
    }

    // PUT /slots/{id}
    public function update(int $slotId): void
    {
        $slot = $this->slotRepo->findById($slotId);
        $sessionProviderId = $_SESSION['provider_id'] ?? null;

        if (!$slot) {
            http_response_code(404);
            echo json_encode(['error' => 'Slot not found']);
            return;
        }

        if ($slot->getProviderId() !== $sessionProviderId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['startTime']) || empty($data['endTime']) || !isset($data['isBooked'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing fields']);
            return;
        }

        $slot->setStartTime(new DateTimeImmutable($data['startTime']));
        $slot->setEndTime(new DateTimeImmutable($data['endTime']));
        $slot->setIsBooked((bool) $data['isBooked']);

        $this->slotRepo->update($slot);

        echo json_encode(['message' => 'Slot updated']);
    }

    // PATCH /slots/{id}
    public function patch(int $slotId): void
    {
        $slot = $this->slotRepo->findById($slotId);
        $sessionProviderId = $_SESSION['provider_id'] ?? null;

        if (!$slot) {
            http_response_code(404);
            echo json_encode(['error' => 'Slot not found']);
            return;
        }

        if ($slot->getProviderId() !== $sessionProviderId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['startTime'])) {
            $slot->setStartTime(new DateTimeImmutable($data['startTime']));
        }

        if (isset($data['endTime'])) {
            $slot->setEndTime(new DateTimeImmutable($data['endTime']));
        }

        if (isset($data['isBooked'])) {
            $slot->setIsBooked((bool) $data['isBooked']);
        }

        $this->slotRepo->update($slot);

        echo json_encode(['message' => 'Slot patched']);
    }

    // DELETE /slots/{id}
    public function destroy(int $slotId): void
    {
        $slot = $this->slotRepo->findById($slotId);
        $sessionProviderId = $_SESSION['provider_id'] ?? null;

        if (!$slot) {
            http_response_code(404);
            echo json_encode(['error' => 'Slot not found']);
            return;
        }

        if ($slot->getProviderId() !== $sessionProviderId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $this->slotRepo->delete($slotId);
        echo json_encode(['message' => 'Slot deleted']);
    }
}
