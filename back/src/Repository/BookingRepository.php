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
        $stmt->execute([$id]);
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

    public function save(Booking $booking): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO booking (status, client_id, slot_id, created_at)
            VALUES (?, ?, ?, ?)
        ');

        $stmt->execute([
            $booking->getStatus(),
            $booking->getClientId(),
            $booking->getSlotId(),
            $booking->getCreatedAt()->format('Y-m-d H:i:s'),
        ]);

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

        $stmt->execute([
            $booking->getStatus(),
            $booking->getClientId(),
            $booking->getSlotId(),
            $booking->getId(),
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM booking WHERE id = ?');
        $stmt->execute([$id]);
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
