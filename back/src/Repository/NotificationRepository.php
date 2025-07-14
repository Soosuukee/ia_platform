<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Repository;

use Soosuuke\IaPlatform\Entity\Notification;
use Soosuuke\IaPlatform\Config\Database;
use DateTimeImmutable;
use ReflectionClass;

class NotificationRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function findByRecipient(int $recipientId, string $recipientType): array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM notification
            WHERE recipient_id = ? AND recipient_type = ?
            ORDER BY created_at DESC
        ');
        $stmt->execute([$recipientId, $recipientType]);

        $notifications = [];
        while ($row = $stmt->fetch()) {
            $notifications[] = $this->mapToNotification($row);
        }

        return $notifications;
    }

    public function save(Notification $notification): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO notification (recipient_id, recipient_type, message, is_read, created_at)
            VALUES (?, ?, ?, ?, ?)
        ');

        $stmt->execute([
            $notification->getRecipientId(),
            $notification->getRecipientType(),
            $notification->getMessage(),
            (int) $notification->isRead(),
            $notification->getCreatedAt()->format('Y-m-d H:i:s'),
        ]);

        // Injecter l’ID généré si besoin
        $id = (int) $this->pdo->lastInsertId();
        $ref = new ReflectionClass(Notification::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($notification, $id);
    }

    public function markAsRead(int $notificationId): void
    {
        $stmt = $this->pdo->prepare('UPDATE notification SET is_read = 1 WHERE id = ?');
        $stmt->execute([$notificationId]);
    }

    private function mapToNotification(array $data): Notification
    {
        $notification = new Notification(
            (int) $data['recipient_id'],
            $data['recipient_type'],
            $data['message']
        );

        $ref = new ReflectionClass(Notification::class);

        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($notification, (int) $data['id']);

        $createdAtProp = $ref->getProperty('createdAt');
        $createdAtProp->setAccessible(true);
        $createdAtProp->setValue($notification, new DateTimeImmutable($data['created_at']));

        $isReadProp = $ref->getProperty('isRead');
        $isReadProp->setAccessible(true);
        $isReadProp->setValue($notification, (bool) $data['is_read']);

        return $notification;
    }
}
