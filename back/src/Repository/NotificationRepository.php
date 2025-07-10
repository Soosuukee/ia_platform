<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Repository;

use Soosuuke\IaPlatform\Entity\Notification;
use Soosuuke\IaPlatform\Config\Database;
use Soosuuke\IaPlatform\Repository\UserRepository;
use DateTimeImmutable;
use ReflectionClass;

class NotificationRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function findByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM notification WHERE recipient_id = ? ORDER BY created_at DESC');
        $stmt->execute([$userId]);

        $notifications = [];
        while ($row = $stmt->fetch()) {
            $notifications[] = $this->mapToNotification($row);
        }

        return $notifications;
    }

    public function save(Notification $notification): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO notification (recipient_id, message, is_read, created_at)
            VALUES (?, ?, ?, ?)
        ');

        $stmt->execute([
            $notification->getRecipient()->getId(),
            $notification->getMessage(),
            (int) $notification->isRead(),
            $notification->getCreatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    public function markAsRead(int $notificationId): void
    {
        $stmt = $this->pdo->prepare('UPDATE notification SET is_read = 1 WHERE id = ?');
        $stmt->execute([$notificationId]);
    }

    private function mapToNotification(array $data): Notification
    {
        $userRepo = new UserRepository();
        $user = $userRepo->findById((int) $data['recipient_id']);

        $notification = new Notification(
            $user,
            $data['message']
        );

        $ref = new ReflectionClass(Notification::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($notification, (int) $data['id']);

        $createdProp = $ref->getProperty('createdAt');
        $createdProp->setAccessible(true);
        $createdProp->setValue($notification, new DateTimeImmutable($data['created_at']));

        $isReadProp = $ref->getProperty('isRead');
        $isReadProp->setAccessible(true);
        $isReadProp->setValue($notification, (bool) $data['is_read']);

        return $notification;
    }
}
