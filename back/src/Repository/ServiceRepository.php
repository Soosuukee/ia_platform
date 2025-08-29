<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Repository;

use Soosuuke\IaPlatform\Entity\Service;
use Soosuuke\IaPlatform\Config\Database;
use ReflectionClass;

class ServiceRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function findById(int $id): ?Service
    {
        $stmt = $this->pdo->prepare('SELECT * FROM service WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        return $data ? $this->mapToService($data) : null;
    }

    public function findBySlug(string $slug): ?Service
    {
        $stmt = $this->pdo->prepare('SELECT * FROM service WHERE slug = ?');
        $stmt->execute([$slug]);
        $data = $stmt->fetch();

        return $data ? $this->mapToService($data) : null;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM service ORDER BY created_at DESC');
        $services = [];

        while ($row = $stmt->fetch()) {
            $services[] = $this->mapToService($row);
        }

        return $services;
    }

    public function findByProviderId(int $providerId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM service WHERE provider_id = ? ORDER BY created_at DESC');
        $stmt->execute([$providerId]);

        $services = [];
        while ($row = $stmt->fetch()) {
            $services[] = $this->mapToService($row);
        }

        return $services;
    }

    public function findByProviderSlug(string $providerSlug): array
    {
        $stmt = $this->pdo->prepare('
            SELECT s.* FROM service s
            INNER JOIN provider p ON s.provider_id = p.id
            WHERE p.slug = ?
            ORDER BY s.created_at DESC
        ');
        $stmt->execute([$providerSlug]);

        $services = [];
        while ($row = $stmt->fetch()) {
            $services[] = $this->mapToService($row);
        }

        return $services;
    }

    public function findActive(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM service WHERE is_active = 1 ORDER BY created_at DESC');
        $services = [];

        while ($row = $stmt->fetch()) {
            $services[] = $this->mapToService($row);
        }

        return $services;
    }

    public function findFeatured(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM service WHERE is_featured = 1 AND is_active = 1 ORDER BY created_at DESC');
        $services = [];

        while ($row = $stmt->fetch()) {
            $services[] = $this->mapToService($row);
        }

        return $services;
    }

    public function save(Service $service): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO service (provider_id, title, summary, max_price, min_price, is_active, is_featured, cover, slug)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');

        $stmt->execute([
            $service->getProviderId(),
            $service->getTitle(),
            $service->getSummary(),
            $service->getMaxPrice(),
            $service->getMinPrice(),
            (int) $service->isActive(),
            (int) $service->isFeatured(),
            $service->getCover(),
            $service->getSlug()
        ]);

        $id = (int) $this->pdo->lastInsertId();
        $ref = new ReflectionClass(Service::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($service, $id);
    }

    public function update(Service $service): void
    {
        $stmt = $this->pdo->prepare('
            UPDATE service
            SET title = ?, summary = ?, max_price = ?, min_price = ?, is_active = ?, is_featured = ?, cover = ?, slug = ?
            WHERE id = ?
        ');

        $stmt->execute([
            $service->getTitle(),
            $service->getSummary(),
            $service->getMaxPrice(),
            $service->getMinPrice(),
            (int) $service->isActive(),
            (int) $service->isFeatured(),
            $service->getCover(),
            $service->getSlug(),
            $service->getId()
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM service WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function deleteByProviderId(int $providerId): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM service WHERE provider_id = ?");
        $stmt->execute([$providerId]);
    }

    public function findByTagId(int $tagId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT s.* FROM service s
            INNER JOIN service_tag st ON s.id = st.service_id
            WHERE st.tag_id = ?
            ORDER BY s.created_at DESC
        ');
        $stmt->execute([$tagId]);

        $services = [];
        while ($row = $stmt->fetch()) {
            $services[] = $this->mapToService($row);
        }

        return $services;
    }

    public function findByTagSlug(string $tagSlug): array
    {
        $stmt = $this->pdo->prepare('
            SELECT s.* FROM service s
            INNER JOIN service_tag st ON s.id = st.service_id
            INNER JOIN tag t ON st.tag_id = t.id
            WHERE t.slug = ?
            ORDER BY s.created_at DESC
        ');
        $stmt->execute([$tagSlug]);

        $services = [];
        while ($row = $stmt->fetch()) {
            $services[] = $this->mapToService($row);
        }

        return $services;
    }

    public function getServiceWithContent(int $serviceId): ?array
    {
        // Récupérer le service
        $service = $this->findById($serviceId);
        if (!$service) {
            return null;
        }

        // Récupérer les sections
        $stmt = $this->pdo->prepare('SELECT * FROM service_section WHERE service_id = ? ORDER BY id');
        $stmt->execute([$serviceId]);
        $sections = $stmt->fetchAll();

        $serviceData = [
            'service' => $service->toArray(),
            'sections' => []
        ];

        foreach ($sections as $section) {
            $sectionData = [
                'section' => $section,
                'contents' => []
            ];

            // Récupérer le contenu de chaque section
            $stmt = $this->pdo->prepare('SELECT * FROM service_content WHERE service_content_id = ? ORDER BY id');
            $stmt->execute([$section['id']]);
            $contents = $stmt->fetchAll();

            foreach ($contents as $content) {
                $contentData = [
                    'content' => $content,
                    'images' => []
                ];

                // Récupérer les images de chaque contenu
                $stmt = $this->pdo->prepare('SELECT * FROM service_image WHERE service_content_id = ? ORDER BY id');
                $stmt->execute([$content['id']]);
                $images = $stmt->fetchAll();

                $contentData['images'] = $images;
                $sectionData['contents'][] = $contentData;
            }

            $serviceData['sections'][] = $sectionData;
        }

        return $serviceData;
    }

    public function saveServiceWithContent(Service $service, array $sections): void
    {
        $this->pdo->beginTransaction();

        try {
            // Sauvegarder le service
            if ($service->getId()) {
                $this->update($service);
                $serviceId = $service->getId();
            } else {
                $this->save($service);
                $serviceId = $service->getId();
            }

            // Supprimer l'ancien contenu
            $stmt = $this->pdo->prepare('DELETE FROM service_image WHERE service_content_id IN (SELECT id FROM service_content WHERE service_content_id IN (SELECT id FROM service_section WHERE service_id = ?))');
            $stmt->execute([$serviceId]);

            $stmt = $this->pdo->prepare('DELETE FROM service_content WHERE service_content_id IN (SELECT id FROM service_section WHERE service_id = ?)');
            $stmt->execute([$serviceId]);

            $stmt = $this->pdo->prepare('DELETE FROM service_section WHERE service_id = ?');
            $stmt->execute([$serviceId]);

            // Sauvegarder les nouvelles sections
            foreach ($sections as $sectionData) {
                $stmt = $this->pdo->prepare('INSERT INTO service_section (service_id, title) VALUES (?, ?)');
                $stmt->execute([$serviceId, $sectionData['title']]);
                $sectionId = (int) $this->pdo->lastInsertId();

                // Sauvegarder le contenu de la section
                if (isset($sectionData['contents'])) {
                    foreach ($sectionData['contents'] as $contentData) {
                        $stmt = $this->pdo->prepare('INSERT INTO service_content (service_content_id, content) VALUES (?, ?)');
                        $stmt->execute([$sectionId, $contentData['content']]);
                        $contentId = (int) $this->pdo->lastInsertId();

                        // Sauvegarder les images du contenu
                        if (isset($contentData['images'])) {
                            foreach ($contentData['images'] as $imageData) {
                                $stmt = $this->pdo->prepare('INSERT INTO service_image (service_content_id, url) VALUES (?, ?)');
                                $stmt->execute([$contentId, $imageData['url']]);
                            }
                        }
                    }
                }
            }

            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    private function mapToService(array $data): Service
    {
        $service = new Service(
            (int)$data['provider_id'],
            $data['title'] ?? '',
            $data['max_price'] !== null ? (float)$data['max_price'] : null,
            $data['min_price'] !== null ? (float)$data['min_price'] : null,
            (bool)($data['is_active'] ?? false),
            (bool)($data['is_featured'] ?? false),
            $data['cover'],
            $data['summary'],
            $data['slug']
        );

        $ref = new ReflectionClass(Service::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($service, (int) $data['id']);

        return $service;
    }
}
