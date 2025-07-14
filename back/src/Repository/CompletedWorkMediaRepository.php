<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Repository;

use Soosuuke\IaPlatform\Config\Database;
use Soosuuke\IaPlatform\Entity\CompletedWorkMedia;
use ReflectionClass;

class CompletedWorkMediaRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function findById(int $id): ?CompletedWorkMedia
    {
        $stmt = $this->pdo->prepare('SELECT * FROM completed_work_media WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        return $data ? $this->mapToMedia($data) : null;
    }

    public function findAllByWorkId(int $workId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM completed_work_media WHERE work_id = ?');
        $stmt->execute([$workId]);

        $medias = [];
        while ($row = $stmt->fetch()) {
            $medias[] = $this->mapToMedia($row);
        }

        return $medias;
    }

    public function save(CompletedWorkMedia $media): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO completed_work_media (work_id, media_type, media_url)
            VALUES (?, ?, ?)
        ');

        $stmt->execute([
            $media->getWorkId(),
            $media->getMediaType(),
            $media->getMediaUrl()
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM completed_work_media WHERE id = ?');
        $stmt->execute([$id]);
    }

    private function mapToMedia(array $data): CompletedWorkMedia
    {
        $media = new CompletedWorkMedia(
            (int) $data['work_id'],
            $data['media_type'],
            $data['media_url']
        );

        $ref = new ReflectionClass(CompletedWorkMedia::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($media, (int) $data['id']);

        return $media;
    }
}
