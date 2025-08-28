<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Controller;

use Soosuuke\IaPlatform\Repository\ProviderRepository;
use Soosuuke\IaPlatform\Repository\CountryRepository;
use Soosuuke\IaPlatform\Repository\ProviderSoftSkillRepository;
use Soosuuke\IaPlatform\Repository\ProviderHardSkillRepository;
use Soosuuke\IaPlatform\Repository\ProviderJobRepository;
use Soosuuke\IaPlatform\Repository\ProviderLanguageRepository;
use Soosuuke\IaPlatform\Repository\EducationRepository;
use Soosuuke\IaPlatform\Repository\ExperienceRepository;
use Soosuuke\IaPlatform\Repository\CompletedWorkRepository;
use Soosuuke\IaPlatform\Entity\Provider;
use Soosuuke\IaPlatform\Service\ProviderSlugificationService;
use Soosuuke\IaPlatform\Service\FileUploadService;

class ProviderController
{
    private ProviderRepository $providerRepository;
    private CountryRepository $countryRepository;
    private ProviderSoftSkillRepository $softSkillRepository;
    private ProviderHardSkillRepository $hardSkillRepository;
    private ProviderJobRepository $jobRepository;
    private ProviderLanguageRepository $languageRepository;
    private EducationRepository $educationRepository;
    private ExperienceRepository $experienceRepository;
    private CompletedWorkRepository $completedWorkRepository;
    private ProviderSlugificationService $slugificationService;
    private FileUploadService $fileUploadService;

    public function __construct()
    {
        $this->providerRepository = new ProviderRepository();
        $this->countryRepository = new CountryRepository();
        $this->softSkillRepository = new ProviderSoftSkillRepository();
        $this->hardSkillRepository = new ProviderHardSkillRepository();
        $this->jobRepository = new ProviderJobRepository();
        $this->languageRepository = new ProviderLanguageRepository();
        $this->educationRepository = new EducationRepository();
        $this->experienceRepository = new ExperienceRepository();
        $this->completedWorkRepository = new CompletedWorkRepository();
        $this->slugificationService = new ProviderSlugificationService();
        $this->fileUploadService = new FileUploadService();
    }

    // GET /providers
    public function getAllProviders(): array
    {
        return $this->providerRepository->findAll();
    }

    // GET /providers/{id}
    public function getProviderById(int $id): ?Provider
    {
        return $this->providerRepository->findById($id);
    }

    // GET /providers/email/{email}
    public function getProviderByEmail(string $email): ?Provider
    {
        return $this->providerRepository->findByEmail($email);
    }

    // GET /providers/slug/{slug}
    public function getProviderBySlug(string $slug): ?Provider
    {
        return $this->providerRepository->findBySlug($slug);
    }

    /**
     * Upload une photo de profil pour un provider
     */
    public function uploadProfilePicture(int $providerId, array $file): array
    {
        try {
            // Vérifier que le provider existe
            $provider = $this->providerRepository->findById($providerId);
            if (!$provider) {
                return [
                    'success' => false,
                    'message' => 'Provider non trouvé'
                ];
            }

            // Supprimer l'ancienne photo si elle existe
            if ($provider->getProfilePicture()) {
                $this->fileUploadService->deleteFile($provider->getProfilePicture());
            }

            // Upload de la nouvelle photo
            $newProfilePictureUrl = $this->fileUploadService->uploadProviderProfilePicture(
                $file,
                $providerId
            );

            // Mettre à jour le provider en base
            $provider->setProfilePicture($newProfilePictureUrl);
            $this->providerRepository->update($provider);

            return [
                'success' => true,
                'message' => 'Photo de profil mise à jour avec succès',
                'profilePicture' => $newProfilePictureUrl
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'upload: ' . $e->getMessage()
            ];
        }
    }

    // POST /providers
    public function createProvider(array $data): Provider
    {
        // Générer le slug automatiquement
        $slug = $this->slugificationService->generateProviderSlug(
            $data['firstName'],
            $data['lastName'],
            function ($slug) {
                return $this->providerRepository->findBySlug($slug) !== null;
            }
        );

        $provider = new Provider(
            $data['firstName'],
            $data['lastName'],
            $data['email'],
            $data['password'],
            $data['countryId'],
            $data['city'],
            $data['profilePicture'] ?? null,
            $slug,
            $data['state'] ?? null,
            $data['postalCode'] ?? null,
            $data['address'] ?? null
        );

        $this->providerRepository->save($provider);
        return $provider;
    }

    // PUT /providers/{id}
    public function updateProvider(int $id, array $data): ?Provider
    {
        $provider = $this->providerRepository->findById($id);
        if (!$provider) {
            return null;
        }

        // Mise à jour des propriétés
        $provider = new Provider(
            $data['firstName'] ?? $provider->getFirstName(),
            $data['lastName'] ?? $provider->getLastName(),
            $data['email'] ?? $provider->getEmail(),
            $data['countryId'] ?? $provider->getCountryId(),
            $data['city'] ?? $provider->getCity(),
            $data['profilePicture'] ?? $provider->getProfilePicture(),
            $data['slug'] ?? $provider->getSlug(),
            $data['state'] ?? $provider->getState(),
            $data['postalCode'] ?? $provider->getPostalCode(),
            $data['address'] ?? $provider->getAddress()
        );

        $this->providerRepository->update($provider);
        return $provider;
    }

    // DELETE /providers/{id}
    public function deleteProvider(int $id): bool
    {
        $provider = $this->providerRepository->findById($id);
        if (!$provider) {
            return false;
        }

        $this->providerRepository->delete($id);
        return true;
    }

    // GET /providers/{id}/soft-skills
    public function getProviderSoftSkills(int $providerId): array
    {
        return $this->softSkillRepository->findByProviderId($providerId);
    }

    // POST /providers/{id}/soft-skills
    public function addSoftSkillToProvider(int $providerId, int $skillId): bool
    {
        try {
            $this->softSkillRepository->addSkillToProvider($providerId, $skillId);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    // DELETE /providers/{id}/soft-skills/{skillId}
    public function removeSoftSkillFromProvider(int $providerId, int $skillId): bool
    {
        try {
            $this->softSkillRepository->removeSkillFromProvider($providerId, $skillId);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    // GET /providers/{id}/hard-skills
    public function getProviderHardSkills(int $providerId): array
    {
        return $this->hardSkillRepository->findByProviderId($providerId);
    }

    // POST /providers/{id}/hard-skills
    public function addHardSkillToProvider(int $providerId, int $skillId): bool
    {
        try {
            $this->hardSkillRepository->addSkillToProvider($providerId, $skillId);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    // DELETE /providers/{id}/hard-skills/{skillId}
    public function removeHardSkillFromProvider(int $providerId, int $skillId): bool
    {
        try {
            $this->hardSkillRepository->removeSkillFromProvider($providerId, $skillId);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    // GET /providers/{id}/jobs
    public function getProviderJobs(int $providerId): array
    {
        return $this->jobRepository->findByProviderId($providerId);
    }

    // POST /providers/{id}/jobs
    public function addJobToProvider(int $providerId, int $jobId): bool
    {
        try {
            $this->jobRepository->addJobToProvider($providerId, $jobId);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    // DELETE /providers/{id}/jobs/{jobId}
    public function removeJobFromProvider(int $providerId, int $jobId): bool
    {
        try {
            $this->jobRepository->removeJobFromProvider($providerId, $jobId);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    // GET /providers/{id}/languages
    public function getProviderLanguages(int $providerId): array
    {
        return $this->languageRepository->findByProviderId($providerId);
    }

    // POST /providers/{id}/languages
    public function addLanguageToProvider(int $providerId, int $languageId): bool
    {
        try {
            $this->languageRepository->addLanguageToProvider($providerId, $languageId);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    // DELETE /providers/{id}/languages/{languageId}
    public function removeLanguageFromProvider(int $providerId, int $languageId): bool
    {
        try {
            $this->languageRepository->removeLanguageFromProvider($providerId, $languageId);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    // GET /providers/{id}/education
    public function getProviderEducation(int $providerId): array
    {
        return $this->educationRepository->findByProviderId($providerId);
    }

    // GET /providers/{id}/experience
    public function getProviderExperience(int $providerId): array
    {
        return $this->experienceRepository->findByProviderId($providerId);
    }

    // GET /countries
    public function getAllCountries(): array
    {
        return $this->countryRepository->findAll();
    }

    // POST /providers/{providerId}/experience/{experienceId}/logo
    public function uploadExperienceLogo(int $providerId, int $experienceId, array $file): array
    {
        try {
            $experience = $this->experienceRepository->findById($experienceId);
            if (!$experience || $experience->getProviderId() !== $providerId) {
                return [
                    'success' => false,
                    'message' => 'Expérience non trouvée'
                ];
            }

            if ($experience->getCompanyLogo()) {
                $this->fileUploadService->deleteFile($experience->getCompanyLogo());
            }

            $newLogoUrl = $this->fileUploadService->uploadExperienceLogo(
                $file,
                $experienceId
            );

            $experience->setCompanyLogo($newLogoUrl);
            $this->experienceRepository->update($experience);

            return [
                'success' => true,
                'message' => 'Logo d\'entreprise mis à jour avec succès',
                'logo' => $newLogoUrl
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'upload: ' . $e->getMessage()
            ];
        }
    }

    // POST /providers/{providerId}/education/{educationId}/logo
    public function uploadEducationLogo(int $providerId, int $educationId, array $file): array
    {
        try {
            $education = $this->educationRepository->findById($educationId);
            if (!$education || $education->getProviderId() !== $providerId) {
                return [
                    'success' => false,
                    'message' => 'Éducation non trouvée'
                ];
            }

            if ($education->getInstitutionImage()) {
                $this->fileUploadService->deleteFile($education->getInstitutionImage());
            }

            $newLogoUrl = $this->fileUploadService->uploadEducationLogo(
                $file,
                $educationId
            );

            $education->setInstitutionImage($newLogoUrl);
            $this->educationRepository->update($education);

            return [
                'success' => true,
                'message' => 'Logo d\'institution mis à jour avec succès',
                'logo' => $newLogoUrl
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'upload: ' . $e->getMessage()
            ];
        }
    }

    // POST /providers/{providerId}/completed-works/{workId}/media
    public function uploadCompletedWorkMedia(int $providerId, int $workId, array $file): array
    {
        try {
            $work = $this->completedWorkRepository->findById($workId);
            if (!$work || $work->getProviderId() !== $providerId) {
                return [
                    'success' => false,
                    'message' => 'Travail réalisé non trouvé'
                ];
            }

            $newMediaUrl = $this->fileUploadService->uploadCompletedWorkMedia(
                $file,
                $workId
            );

            return [
                'success' => true,
                'message' => 'Média uploadé avec succès',
                'media' => $newMediaUrl
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'upload: ' . $e->getMessage()
            ];
        }
    }
}
