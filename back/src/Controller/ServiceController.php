<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Controller;

use Soosuuke\IaPlatform\Repository\ServiceRepository;
use Soosuuke\IaPlatform\Repository\ProviderRepository;
use Soosuuke\IaPlatform\Entity\Service;
use Soosuuke\IaPlatform\Service\ServiceSlugificationService;
use Soosuuke\IaPlatform\Service\FileUploadService;
use Soosuuke\IaPlatform\Config\AuthMiddleware;

class ServiceController
{
    private ServiceRepository $serviceRepository;
    private ProviderRepository $providerRepository;
    private ServiceSlugificationService $slugificationService;
    private FileUploadService $fileUploadService;

    public function __construct()
    {
        $this->serviceRepository = new ServiceRepository();
        $this->providerRepository = new ProviderRepository();
        $this->slugificationService = new ServiceSlugificationService();
        $this->fileUploadService = new FileUploadService();
    }

    // GET /services
    public function getAllServices(): array
    {
        $services = $this->serviceRepository->findAll();
        return array_map(function (Service $service) {
            return $service->toArray();
        }, $services);
    }

    // GET /services/{id}
    public function getServiceById(int $id): ?array
    {
        return $this->serviceRepository->getServiceWithContent($id);
    }

    // GET /services/provider/{providerSlug}
    public function getServicesByProviderSlug(string $providerSlug): array
    {
        $services = $this->serviceRepository->findByProviderSlug($providerSlug);

        // Retourner les services avec leur contenu complet
        $servicesWithContent = [];
        foreach ($services as $service) {
            $servicesWithContent[] = $this->serviceRepository->getServiceWithContent($service->getId());
        }

        return $servicesWithContent;
    }

    // GET /providers/{providerId}/services
    public function getServicesByProviderId(int $providerId): array
    {
        $services = $this->serviceRepository->findByProviderId($providerId);
        $servicesWithContent = [];
        foreach ($services as $service) {
            $servicesWithContent[] = $this->serviceRepository->getServiceWithContent($service->getId());
        }
        return $servicesWithContent;
    }

    // GET /services/active
    public function getActiveServices(): array
    {
        $services = $this->serviceRepository->findActive();
        return array_map(fn(Service $s) => $s->toArray(), $services);
    }

    // GET /services/featured
    public function getFeaturedServices(): array
    {
        $services = $this->serviceRepository->findFeatured();
        return array_map(fn(Service $s) => $s->toArray(), $services);
    }

    // POST /services
    public function createService(array $data): Service
    {
        // Générer le slug automatiquement basé sur le titre
        $slug = $this->slugificationService->generateServiceSlug(
            $data['title'] ?? 'service',
            function ($slug) {
                return $this->serviceRepository->findBySlug($slug) !== null;
            }
        );

        $service = new Service(
            (int) $data['providerId'],
            $data['maxPrice'] ?? null,
            $data['minPrice'] ?? null,
            $data['isActive'] ?? true,
            $data['isFeatured'] ?? false,
            $data['cover'] ?? null,
            $data['summary'] ?? null,
            $data['tag'] ?? null,
            $slug
        );

        $this->serviceRepository->save($service);
        return $service;
    }

    // PUT /services/{id}
    public function updateService(int $id, array $data): ?Service
    {
        $service = $this->serviceRepository->findById($id);
        if (!$service) {
            return null;
        }

        // Security: only owner provider can update
        $currentUserId = AuthMiddleware::getCurrentUserId();
        $currentUserType = AuthMiddleware::getCurrentUserType();
        if ($currentUserType !== 'provider' || $service->getProviderId() !== $currentUserId) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Accès interdit']);
            return null;
        }

        // Mise à jour des propriétés
        $service = new Service(
            $data['providerId'] ?? $service->getProviderId(),
            $data['maxPrice'] ?? $service->getMaxPrice(),
            $data['minPrice'] ?? $service->getMinPrice(),
            $data['isActive'] ?? $service->isActive(),
            $data['isFeatured'] ?? $service->isFeatured(),
            $data['cover'] ?? $service->getCover(),
            $data['summary'] ?? $service->getSummary(),
            $data['slug'] ?? $service->getSlug()
        );

        $this->serviceRepository->update($service);
        return $service;
    }

    // DELETE /services/{id}
    public function deleteService(int $id): bool
    {
        $service = $this->serviceRepository->findById($id);
        if (!$service) {
            return false;
        }

        $this->serviceRepository->delete($id);
        return true;
    }

    // PUT/PATCH /services/slug/{slug}
    public function updateServiceBySlug(string $slug, array $data): ?Service
    {
        $service = $this->serviceRepository->findBySlug($slug);
        if (!$service) {
            return null;
        }

        $currentUserId = AuthMiddleware::getCurrentUserId();
        $currentUserType = AuthMiddleware::getCurrentUserType();
        if ($currentUserType !== 'provider' || $service->getProviderId() !== $currentUserId) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Accès interdit']);
            return null;
        }

        $service = new Service(
            $data['providerId'] ?? $service->getProviderId(),
            $data['maxPrice'] ?? $service->getMaxPrice(),
            $data['minPrice'] ?? $service->getMinPrice(),
            $data['isActive'] ?? $service->isActive(),
            $data['isFeatured'] ?? $service->isFeatured(),
            $data['cover'] ?? $service->getCover(),
            $data['summary'] ?? $service->getSummary(),
            $data['slug'] ?? $service->getSlug()
        );

        $this->serviceRepository->update($service);
        return $service;
    }

    // DELETE /services/slug/{slug}
    public function deleteServiceBySlug(string $slug): bool
    {
        $service = $this->serviceRepository->findBySlug($slug);
        if (!$service) {
            return false;
        }

        $currentUserId = AuthMiddleware::getCurrentUserId();
        $currentUserType = AuthMiddleware::getCurrentUserType();
        if ($currentUserType !== 'provider' || $service->getProviderId() !== $currentUserId) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Accès interdit']);
            return false;
        }

        $this->serviceRepository->delete($service->getId());
        return true;
    }

    // GET /services/{id}/with-content
    public function getServiceWithContent(int $id): ?array
    {
        return $this->serviceRepository->getServiceWithContent($id);
    }

    // POST /services/with-content
    public function createServiceWithContent(array $data): ?Service
    {
        $service = new Service(
            (int) $data['providerId'],
            $data['maxPrice'] ?? null,
            $data['minPrice'] ?? null,
            $data['isActive'] ?? true,
            $data['isFeatured'] ?? false,
            $data['cover'] ?? null,
            $data['summary'] ?? null,
            $data['tag'] ?? null,
            $data['slug'] ?? null
        );

        $sections = $data['sections'] ?? [];

        $this->serviceRepository->saveServiceWithContent($service, $sections);
        return $service;
    }

    // PATCH /services/{id}/with-content
    public function patchServiceWithContent(int $id, array $data): ?Service
    {
        $service = $this->serviceRepository->findById($id);
        if (!$service) {
            return null;
        }

        // Security: only owner provider can patch
        $currentUserId = AuthMiddleware::getCurrentUserId();
        $currentUserType = AuthMiddleware::getCurrentUserType();
        if ($currentUserType !== 'provider' || $service->getProviderId() !== $currentUserId) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Accès interdit']);
            return null;
        }

        // Mettre à jour éventuellement quelques métadonnées du service si fournies
        if (isset($data['summary'])) {
            $service->setSummary($data['summary']);
        }

        if (isset($data['isActive'])) {
            $service->setIsActive((bool)$data['isActive']);
        }
        if (isset($data['isFeatured'])) {
            $service->setIsFeatured((bool)$data['isFeatured']);
        }
        if (isset($data['cover'])) {
            $service->setCover($data['cover']);
        }

        $sections = $data['sections'] ?? [];
        $this->serviceRepository->saveServiceWithContent($service, $sections);

        return $service;
    }

    // GET /providers/{providerSlug}/services/{serviceSlug}
    public function getServiceByProviderAndSlug(string $providerSlug, string $serviceSlug): ?array
    {
        // D'abord trouver le provider par son slug
        $provider = $this->providerRepository->findBySlug($providerSlug);
        if (!$provider) {
            return null;
        }

        // Ensuite trouver le service par son slug et le provider ID
        $service = $this->serviceRepository->findBySlug($serviceSlug);
        if (!$service || $service->getProviderId() !== $provider->getId()) {
            return null;
        }

        return $this->serviceRepository->getServiceWithContent($service->getId());
    }



    // POST /services/{id}/cover
    public function uploadCover(int $serviceId, array $file): array
    {
        try {
            $service = $this->serviceRepository->findById($serviceId);
            if (!$service) {
                return [
                    'success' => false,
                    'message' => 'Service non trouvé'
                ];
            }

            // Security: only owner provider can update cover
            $currentUserId = AuthMiddleware::getCurrentUserId();
            $currentUserType = AuthMiddleware::getCurrentUserType();
            if ($currentUserType !== 'provider' || $service->getProviderId() !== $currentUserId) {
                return [
                    'success' => false,
                    'message' => 'Accès interdit'
                ];
            }

            if ($service->getCover()) {
                $this->fileUploadService->deleteFile($service->getCover());
            }

            $newCoverUrl = $this->fileUploadService->uploadServiceCover(
                $file,
                $serviceId
            );

            $service->setCover($newCoverUrl);
            $this->serviceRepository->update($service);

            return [
                'success' => true,
                'message' => 'Couverture mise à jour avec succès',
                'cover' => $newCoverUrl
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'upload: ' . $e->getMessage()
            ];
        }
    }

    // POST /services/{serviceId}/content/{contentId}/images
    public function uploadImage(int $serviceId, int $contentId, array $file): array
    {
        try {
            $service = $this->serviceRepository->findById($serviceId);
            if (!$service) {
                return [
                    'success' => false,
                    'message' => 'Service non trouvé'
                ];
            }

            $newImageUrl = $this->fileUploadService->uploadServiceImage(
                $file,
                $contentId
            );

            return [
                'success' => true,
                'message' => 'Image uploadée avec succès',
                'image' => $newImageUrl
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'upload: ' . $e->getMessage()
            ];
        }
    }

    public function getServicesByTag(int $tagId): array
    {
        $services = $this->serviceRepository->findByTagId($tagId);
        return array_map(fn(Service $service) => [
            'id' => $service->getId(),
            'providerId' => $service->getProviderId(),
            'summary' => $service->getSummary(),
            'maxPrice' => $service->getMaxPrice(),
            'minPrice' => $service->getMinPrice(),
            'isActive' => $service->isActive(),
            'isFeatured' => $service->isFeatured(),
            'cover' => $service->getCover(),
            'slug' => $service->getSlug(),
            'createdAt' => $service->getCreatedAt()->format('Y-m-d H:i:s')
        ], $services);
    }

    // GET /services/tag/slug/{tagSlug}
    public function getServicesByTagSlug(string $tagSlug): array
    {
        $services = $this->serviceRepository->findByTagSlug($tagSlug);
        return array_map(fn(Service $service) => $service->toArray(), $services);
    }

    // GET /services/search/{query}
    public function searchServices(string $query): array
    {
        $services = $this->serviceRepository->search($query);
        return array_map(fn(Service $service) => $service->toArray(), $services);
    }
}
