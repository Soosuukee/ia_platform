<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Controller;

use Soosuuke\IaPlatform\Repository\BookingRepository;
use Soosuuke\IaPlatform\Entity\Booking;

class BookingController
{
    private BookingRepository $bookingRepository;

    public function __construct()
    {
        $this->bookingRepository = new BookingRepository();
    }

    // GET /bookings/{id}
    public function show(int $id): void
    {
        $sessionClientId = $_SESSION['client_id'] ?? null;
        $booking = $this->bookingRepository->findById($id);

        if (!$booking || $booking->getClientId() !== $sessionClientId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized or not found']);
            return;
        }

        echo json_encode([
            'id' => $booking->getId(),
            'status' => $booking->getStatus(),
            'clientId' => $booking->getClientId(),
            'slotId' => $booking->getSlotId(),
            'createdAt' => $booking->getCreatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    // POST /bookings
    public function create(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $sessionClientId = $_SESSION['client_id'] ?? null;

        if (
            !$sessionClientId ||
            empty($data['status']) ||
            empty($data['slotId'])
        ) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing fields']);
            return;
        }

        $booking = new Booking(
            $data['status'],
            (int) $sessionClientId,
            (int) $data['slotId']
        );

        $this->bookingRepository->save($booking);

        http_response_code(201);
        echo json_encode(['message' => 'Booking created', 'id' => $booking->getId()]);
    }

    // PUT /bookings/{id}
    public function update(int $id): void
    {
        $sessionClientId = $_SESSION['client_id'] ?? null;
        $booking = $this->bookingRepository->findById($id);

        if (!$booking || $booking->getClientId() !== $sessionClientId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized or not found']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['status']) || !isset($data['slotId'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing fields for full update']);
            return;
        }

        $booking->setStatus($data['status']);
        $booking->setSlotId((int) $data['slotId']);

        $this->bookingRepository->update($booking);

        echo json_encode(['message' => 'Booking updated']);
    }

    // PATCH /bookings/{id}
    public function patch(int $id): void
    {
        $sessionClientId = $_SESSION['client_id'] ?? null;
        $booking = $this->bookingRepository->findById($id);

        if (!$booking || $booking->getClientId() !== $sessionClientId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized or not found']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['status'])) {
            $booking->setStatus($data['status']);
        }

        if (isset($data['slotId'])) {
            $booking->setSlotId((int) $data['slotId']);
        }

        $this->bookingRepository->update($booking);

        echo json_encode(['message' => 'Booking patched']);
    }

    // DELETE /bookings/{id}
    public function destroy(int $id): void
    {
        $sessionClientId = $_SESSION['client_id'] ?? null;
        $booking = $this->bookingRepository->findById($id);

        if (!$booking || $booking->getClientId() !== $sessionClientId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized or not found']);
            return;
        }

        $this->bookingRepository->delete($id);

        echo json_encode(['message' => 'Booking deleted']);
    }
}
