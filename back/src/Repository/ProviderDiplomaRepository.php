<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Repository;

use PDO;
use Soosuuke\IaPlatform\Entity\ProviderDiploma;
use Soosuuke\IaPlatform\Config\Database;

class ProviderDiplomaRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function save(ProviderDiploma $diploma): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO provider_diploma (title, institution, description, start_date, end_date, provider_id)
            VALUES (:title, :institution, :description, :start_date, :end_date, :provider_id)
        ');

        $stmt->execute([
            'title' => $diploma->getTitle(),
            'institution' => $diploma->getInstitution(),
            'description' => $diploma->getDescription(),
            'start_date' => $diploma->getStartDate()?->format('Y-m-d'),
            'end_date' => $diploma->getEndDate()?->format('Y-m-d'),
            'provider_id' => $diploma->getProviderId(),
        ]);
    }

    public function findById(int $id): ?ProviderDiploma
    {
        $stmt = $this->pdo->prepare('SELECT * FROM provider_diploma WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();

        return $data ? $this->hydrate($data) : null;
    }

    public function findAllByProviderId(int $providerId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM provider_diploma WHERE provider_id = :provider_id');
        $stmt->execute(['provider_id' => $providerId]);

        $results = [];
        while ($row = $stmt->fetch()) {
            $results[] = $this->hydrate($row);
        }
        return $results;
    }

    public function update(ProviderDiploma $diploma): void
    {
        $stmt = $this->pdo->prepare('
            UPDATE provider_diploma
            SET title = :title,
                institution = :institution,
                description = :description,
                start_date = :start_date,
                end_date = :end_date
            WHERE id = :id
        ');

        $stmt->execute([
            'id' => $diploma->getId(),
            'title' => $diploma->getTitle(),
            'institution' => $diploma->getInstitution(),
            'description' => $diploma->getDescription(),
            'start_date' => $diploma->getStartDate()?->format('Y-m-d'),
            'end_date' => $diploma->getEndDate()?->format('Y-m-d'),
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM provider_diploma WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function deleteByProviderId(int $providerId): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM provider_diploma WHERE provider_id = ?");
        $stmt->execute([$providerId]);
    }

    private function hydrate(array $data): ProviderDiploma
    {
        return new ProviderDiploma(
            $data['title'],
            $data['institution'],
            $data['description'],
            $data['start_date'] ? new \DateTime($data['start_date']) : null,
            $data['end_date'] ? new \DateTime($data['end_date']) : null,
            (int) $data['provider_id']
        );
    }
}
