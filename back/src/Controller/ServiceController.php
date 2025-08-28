<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Controller;

use Soosuuke\IaPlatform\Repository\ServiceRepository;
use Soosuuke\IaPlatform\Repository\ProviderRepository;
use Soosuuke\IaPlatform\Entity\Service;
use Soosuuke\IaPlatform\Service\ServiceSlugificationService;
use Soosuuke\IaPlatform\Service\FileUploadService;

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
        return $this->serviceRepository->findAll();
    }

    // GET /services/{id}
    public function getServiceById(int $id): ?Service
    {
        return $this->serviceRepository->findById($id);
    }

    // GET /services/slug/{slug}
    public function getServiceBySlug(string $slug): ?Service
    {
        return $this->serviceRepository->findBySlug($slug);
    }

    // GET /services/provider/{providerId}
    public function getServicesByProviderId(int $providerId): array
    {
        return $this->serviceRepository->findByProviderId($providerId);
    }

    // GET /services/active
    public function getActiveServices(): array
    {
        return $this->serviceRepository->findActive();
    }

    // GET /services/featured
    public function getFeaturedServices(): array
    {
        return $this->serviceRepository->findFeatured();
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

        // Mise à jour des propriétés
        $service = new Service(
            $data['providerId'] ?? $service->getProviderId(),
            $data['maxPrice'] ?? $service->getMaxPrice(),
            $data['minPrice'] ?? $service->getMinPrice(),
            $data['isActive'] ?? $service->isActive(),
            $data['isFeatured'] ?? $service->isFeatured(),
            $data['cover'] ?? $service->getCover(),
            $data['summary'] ?? $service->getSummary(),
            $data['tag'] ?? $service->getTag(),
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

    // GET /providers/{providerSlug}/services/{serviceSlug}
    public function getServiceByProviderAndSlug(string $providerSlug, string $serviceSlug): ?Service
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

        return $service;
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
}
