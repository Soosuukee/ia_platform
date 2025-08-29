<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Controller;

use Soosuuke\IaPlatform\Service\ProviderImageService;

class ProviderImageController
{
    private ProviderImageService $imageService;

    public function __construct()
    {
        $this->imageService = new ProviderImageService();
    }

    /**
     * Upload de la cover d'un service
     */
    public function uploadServiceCover(int $providerId, int $serviceId): array
    {
        if (!isset($_FILES['cover'])) {
            return [
                'success' => false,
                'message' => 'Aucun fichier uploadé',
                'error' => 'NO_FILE_UPLOADED'
            ];
        }

        $file = $_FILES['cover'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'upload: ' . $this->getUploadErrorMessage($file['error']),
                'error' => 'UPLOAD_ERROR'
            ];
        }

        return $this->imageService->uploadServiceCover(
            $providerId,
            $serviceId,
            $file['tmp_name'],
            $file['name'],
            true
        );
    }

    /**
     * Upload d'une image de contenu de service
     */
    public function uploadServiceContentImage(int $providerId, int $serviceId, int $sectionId, int $contentId): array
    {
        if (!isset($_FILES['content_image'])) {
            return [
                'success' => false,
                'message' => 'Aucun fichier uploadé',
                'error' => 'NO_FILE_UPLOADED'
            ];
        }

        $file = $_FILES['content_image'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'upload: ' . $this->getUploadErrorMessage($file['error']),
                'error' => 'UPLOAD_ERROR'
            ];
        }

        return $this->imageService->uploadServiceContentImage(
            $providerId,
            $serviceId,
            $sectionId,
            $contentId,
            $file['tmp_name'],
            $file['name']
        );
    }

    /**
     * Upload d'une image pour article/experience/education
     */
    public function uploadEntityImage(int $providerId, string $entityType, int $entityId): array
    {
        if (!isset($_FILES['entity_image'])) {
            return [
                'success' => false,
                'message' => 'Aucun fichier uploadé',
                'error' => 'NO_FILE_UPLOADED'
            ];
        }

        $file = $_FILES['entity_image'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'upload: ' . $this->getUploadErrorMessage($file['error']),
                'error' => 'UPLOAD_ERROR'
            ];
        }

        return $this->imageService->uploadEntityImage(
            $providerId,
            $entityType,
            $entityId,
            $file['tmp_name'],
            $file['name']
        );
    }

    /**
     * Upload d'une image de profil
     */
    public function uploadProfileImage(int $providerId): array
    {
        if (!isset($_FILES['profile_image'])) {
            return [
                'success' => false,
                'message' => 'Aucun fichier uploadé',
                'error' => 'NO_FILE_UPLOADED'
            ];
        }

        $file = $_FILES['profile_image'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'upload: ' . $this->getUploadErrorMessage($file['error']),
                'error' => 'UPLOAD_ERROR'
            ];
        }

        return $this->imageService->uploadProviderImage(
            $providerId,
            'profile',
            $file['tmp_name'],
            $file['name'],
            null,
            true // Remplacer l'image existante
        );
    }

    /**
     * Upload d'une image de service
     */
    public function uploadServiceImage(int $providerId, int $serviceId): array
    {
        if (!isset($_FILES['service_image'])) {
            return [
                'success' => false,
                'message' => 'Aucun fichier uploadé',
                'error' => 'NO_FILE_UPLOADED'
            ];
        }

        $file = $_FILES['service_image'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'upload: ' . $this->getUploadErrorMessage($file['error']),
                'error' => 'UPLOAD_ERROR'
            ];
        }

        return $this->imageService->uploadProviderImage(
            $providerId,
            'services',
            $file['tmp_name'],
            $file['name'],
            $serviceId
        );
    }

    /**
     * Upload d'une image d'article
     */
    public function uploadArticleImage(int $providerId, int $articleId): array
    {
        if (!isset($_FILES['article_image'])) {
            return [
                'success' => false,
                'message' => 'Aucun fichier uploadé',
                'error' => 'NO_FILE_UPLOADED'
            ];
        }

        $file = $_FILES['article_image'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'upload: ' . $this->getUploadErrorMessage($file['error']),
                'error' => 'UPLOAD_ERROR'
            ];
        }

        return $this->imageService->uploadProviderImage(
            $providerId,
            'articles',
            $file['tmp_name'],
            $file['name'],
            $articleId
        );
    }

    /**
     * Upload d'une image d'expérience
     */
    public function uploadExperienceImage(int $providerId, int $experienceId): array
    {
        if (!isset($_FILES['experience_image'])) {
            return [
                'success' => false,
                'message' => 'Aucun fichier uploadé',
                'error' => 'NO_FILE_UPLOADED'
            ];
        }

        $file = $_FILES['experience_image'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'upload: ' . $this->getUploadErrorMessage($file['error']),
                'error' => 'UPLOAD_ERROR'
            ];
        }

        return $this->imageService->uploadProviderImage(
            $providerId,
            'experiences',
            $file['tmp_name'],
            $file['name'],
            $experienceId
        );
    }

    /**
     * Upload d'une image d'éducation
     */
    public function uploadEducationImage(int $providerId, int $educationId): array
    {
        if (!isset($_FILES['education_image'])) {
            return [
                'success' => false,
                'message' => 'Aucun fichier uploadé',
                'error' => 'NO_FILE_UPLOADED'
            ];
        }

        $file = $_FILES['education_image'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'upload: ' . $this->getUploadErrorMessage($file['error']),
                'error' => 'UPLOAD_ERROR'
            ];
        }

        return $this->imageService->uploadProviderImage(
            $providerId,
            'education',
            $file['tmp_name'],
            $file['name'],
            $educationId
        );
    }

    /**
     * Supprimer une image
     */
    public function deleteImage(int $providerId, string $imageType, string $filename, ?int $subId = null): array
    {
        return $this->imageService->deleteProviderImage($providerId, $imageType, $filename, $subId);
    }

    /**
     * Lister les images d'un provider
     */
    public function listImages(int $providerId, string $imageType, ?int $subId = null): array
    {
        return $this->imageService->getProviderImages($providerId, $imageType, $subId);
    }

    /**
     * Lister les images d'un provider pour un sous-id explicite
     */
    public function listImagesBySub(int $providerId, string $imageType, int $subId): array
    {
        return $this->imageService->getProviderImages($providerId, $imageType, $subId);
    }

    /**
     * Supprimer une image en précisant le sous-id (service/article/experience/education)
     */
    public function deleteImageBySub(int $providerId, string $imageType, int $subId, string $filename): array
    {
        return $this->imageService->deleteProviderImage($providerId, $imageType, $filename, $subId);
    }

    /**
     * Obtenir le message d'erreur d'upload
     */
    private function getUploadErrorMessage(int $errorCode): string
    {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return 'Le fichier dépasse la taille maximale autorisée par le serveur';
            case UPLOAD_ERR_FORM_SIZE:
                return 'Le fichier dépasse la taille maximale autorisée par le formulaire';
            case UPLOAD_ERR_PARTIAL:
                return 'Le fichier n\'a été que partiellement uploadé';
            case UPLOAD_ERR_NO_FILE:
                return 'Aucun fichier n\'a été uploadé';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Dossier temporaire manquant';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Échec de l\'écriture du fichier sur le disque';
            case UPLOAD_ERR_EXTENSION:
                return 'Une extension PHP a arrêté l\'upload du fichier';
            default:
                return 'Erreur inconnue lors de l\'upload';
        }
    }
}
