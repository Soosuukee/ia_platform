<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Repository;

use Soosuuke\IaPlatform\Entity\AvailabilitySlot;
use Soosuuke\IaPlatform\Config\Database;
use DateTimeImmutable;
use ReflectionClass;

class AvailabilitySlotRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function findAllByProviderId(int $providerId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM availability_slot WHERE provider_id = ?');
        $stmt->execute([$providerId]);

        $slots = [];
        while ($row = $stmt->fetch()) {
            $slots[] = $this->mapToSlot($row);
        }

        return $slots;
    }

    public function findAvailableByProviderId(int $providerId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM availability_slot 
            WHERE provider_id = ? AND is_booked = 0
        ');
        $stmt->execute([$providerId]);

        $slots = [];
        while ($row = $stmt->fetch()) {
            $slots[] = $this->mapToSlot($row);
        }

        return $slots;
    }

    public function findById(int $id): ?AvailabilitySlot
    {
        $stmt = $this->pdo->prepare('SELECT * FROM availability_slot WHERE id = ?');
        $stmt->execute([$id]);

        $data = $stmt->fetch();
        return $data ? $this->mapToSlot($data) : null;
    }

    public function saveForProvider(int $providerId, AvailabilitySlot $slot): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO availability_slot (provider_id, start_time, end_time, is_booked)
            VALUES (?, ?, ?, ?)
        ');

        $stmt->execute([
            $providerId,
            $slot->getStartTime()->format('Y-m-d H:i:s'),
            $slot->getEndTime()->format('Y-m-d H:i:s'),
            (int) $slot->isBooked(),
        ]);
    }

    public function update(AvailabilitySlot $slot): void
    {
        $stmt = $this->pdo->prepare('
        UPDATE availability_slot
        SET start_time = ?, end_time = ?, is_booked = ?
        WHERE id = ?
    ');

        $stmt->execute([
            $slot->getStartTime()->format('Y-m-d H:i:s'),
            $slot->getEndTime()->format('Y-m-d H:i:s'),
            (int) $slot->isBooked(),
            $slot->getId()
        ]);
    }

    public function delete(int $slotId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM availability_slot WHERE id = ?');
        $stmt->execute([$slotId]);
    }

    public function markAsBooked(int $slotId): void
    {
        $stmt = $this->pdo->prepare('
            UPDATE availability_slot SET is_booked = 1 WHERE id = ?
        ');
        $stmt->execute([$slotId]);
    }

    private function mapToSlot(array $data): AvailabilitySlot
    {
        $slot = new AvailabilitySlot(
            (int) $data['provider_id'],
            new DateTimeImmutable($data['start_time']),
            new DateTimeImmutable($data['end_time']),
            (bool) $data['is_booked']
        );

        $ref = new ReflectionClass(AvailabilitySlot::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($slot, (int) $data['id']);

        return $slot;
    }
}
