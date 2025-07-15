<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Repository;

use Soosuuke\IaPlatform\Entity\Booking;
use Soosuuke\IaPlatform\Config\Database;
use DateTimeImmutable;
use ReflectionClass;

class BookingRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function findById(int $id): ?Booking
    {
        $stmt = $this->pdo->prepare('SELECT * FROM booking WHERE id = ?');
        if (!$stmt->execute([$id])) {
            throw new \RuntimeException("Failed to fetch booking with ID $id");
        }
        $data = $stmt->fetch();

        return $data ? $this->mapToBooking($data) : null;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM booking');
        $bookings = [];

        while ($row = $stmt->fetch()) {
            $bookings[] = $this->mapToBooking($row);
        }

        return $bookings;
    }

    public function findAllByClientId(int $clientId, int $limit = 10): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM booking WHERE client_id = ? ORDER BY created_at DESC LIMIT ?');
        if (!$stmt->execute([$clientId, $limit])) {
            throw new \RuntimeException("Failed to fetch bookings for client ID $clientId");
        }

        $bookings = [];
        while ($row = $stmt->fetch()) {
            $bookings[] = $this->mapToBooking($row);
        }

        return $bookings;
    }

    public function save(Booking $booking): void
    {
        $checkStmt = $this->pdo->prepare('SELECT COUNT(*) FROM availability_slot WHERE id = ?');
        if (!$checkStmt->execute([$booking->getSlotId()])) {
            throw new \RuntimeException("Failed to validate slot ID {$booking->getSlotId()}");
        }
        if ($checkStmt->fetchColumn() == 0) {
            throw new \InvalidArgumentException('Invalid slot_id');
        }

        $stmt = $this->pdo->prepare('
            INSERT INTO booking (status, client_id, slot_id, created_at)
            VALUES (?, ?, ?, ?)
        ');
        if (!$stmt->execute([
            $booking->getStatus(),
            $booking->getClientId(),
            $booking->getSlotId(),
            $booking->getCreatedAt()->format('Y-m-d H:i:s'),
        ])) {
            throw new \RuntimeException('Failed to save booking');
        }

        $bookingId = (int) $this->pdo->lastInsertId();

        $ref = new ReflectionClass(Booking::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($booking, $bookingId);
    }

    public function update(Booking $booking): void
    {
        $stmt = $this->pdo->prepare('
            UPDATE booking
            SET status = ?, client_id = ?, slot_id = ?
            WHERE id = ?
        ');
        if (!$stmt->execute([
            $booking->getStatus(),
            $booking->getClientId(),
            $booking->getSlotId(),
            $booking->getId(),
        ])) {
            throw new \RuntimeException("Failed to update booking with ID {$booking->getId()}");
        }
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM booking WHERE id = ?');
        if (!$stmt->execute([$id])) {
            throw new \RuntimeException("Failed to delete booking with ID $id");
        }
    }

    public function deleteByClientId(int $clientId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM booking WHERE client_id = ?');
        if (!$stmt->execute([$clientId])) {
            throw new \RuntimeException("Failed to delete bookings for client ID $clientId");
        }
    }

    private function mapToBooking(array $data): Booking
    {
        $booking = new Booking(
            $data['status'],
            (int) $data['client_id'],
            (int) $data['slot_id']
        );

        $ref = new ReflectionClass(Booking::class);

        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($booking, (int) $data['id']);

        $createdAtProp = $ref->getProperty('createdAt');
        $createdAtProp->setAccessible(true);
        $createdAtProp->setValue($booking, new DateTimeImmutable($data['created_at']));

        return $booking;
    }
}
