<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Repository;

use Soosuuke\IaPlatform\Entity\Booking;
use Soosuuke\IaPlatform\Config\Database;
use Soosuuke\IaPlatform\Repository\UserRepository;
use Soosuuke\IaPlatform\Repository\AvailabilitySlotRepository;
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

    public function findByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM booking WHERE user_id = ?');
        $stmt->execute([$userId]);

        $bookings = [];
        while ($row = $stmt->fetch()) {
            $bookings[] = $this->mapToBooking($row);
        }

        return $bookings;
    }

    public function save(Booking $booking): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO booking (user_id, slot_id, created_at)
            VALUES (?, ?, ?)
        ');

        $stmt->execute([
            $booking->getClient()->getId(),
            $booking->getSlot()->getId(),
            $booking->getCreatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    private function mapToBooking(array $data): Booking
    {
        $userRepo = new UserRepository();
        $slotRepo = new AvailabilitySlotRepository();

        $user = $userRepo->findById((int) $data['user_id']);
        $slot = $slotRepo->findById((int) $data['slot_id']);
        $status = $data['status'];

        $booking = new Booking($status, $user, $slot);

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
