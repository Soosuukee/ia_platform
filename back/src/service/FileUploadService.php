<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Service;

use Soosuuke\IaPlatform\Config\UploadConfig;

class FileUploadService
{
    /**
     * Upload une photo de profil de provider
     */
    public function uploadProviderProfilePicture(array $file, int $providerId): string
    {
        return $this->uploadFile($file, UploadConfig::PROVIDER_PROFILE_DIR, 'provider-' . $providerId);
    }

    /**
     * Upload une photo de profil de client
     */
    public function uploadClientProfilePicture(array $file, int $clientId): string
    {
        return $this->uploadFile($file, UploadConfig::CLIENT_PROFILE_DIR, 'client-' . $clientId);
    }

    /**
     * Upload une image de couverture de service
     */
    public function uploadServiceCover(array $file, int $serviceId): string
    {
        return $this->uploadFile($file, UploadConfig::PROVIDER_SERVICE_COVER_DIR, 'service-' . $serviceId);
    }

    /**
     * Upload une image de contenu de service
     */
    public function uploadServiceImage(array $file, int $serviceId): string
    {
        return $this->uploadFile($file, UploadConfig::PROVIDER_SERVICE_IMAGE_DIR, 'service-' . $serviceId);
    }

    /**
     * Upload une image de couverture d'article
     */
    public function uploadArticleCover(array $file, int $articleId): string
    {
        return $this->uploadFile($file, UploadConfig::PROVIDER_ARTICLE_COVER_DIR, 'article-' . $articleId);
    }

    /**
     * Upload une image de contenu d'article
     */
    public function uploadArticleImage(array $file, int $articleId): string
    {
        return $this->uploadFile($file, UploadConfig::PROVIDER_ARTICLE_IMAGE_DIR, 'article-' . $articleId);
    }

    /**
     * Upload un logo d'entreprise (experience)
     */
    public function uploadExperienceLogo(array $file, int $experienceId): string
    {
        return $this->uploadFile($file, UploadConfig::PROVIDER_EXPERIENCE_DIR, 'experience-' . $experienceId);
    }

    /**
     * Upload un logo d'institution (education)
     */
    public function uploadEducationLogo(array $file, int $educationId): string
    {
        return $this->uploadFile($file, UploadConfig::PROVIDER_EDUCATION_DIR, 'education-' . $educationId);
    }

    /**
     * Upload un média de travail réalisé
     */
    public function uploadCompletedWorkMedia(array $file, int $workId): string
    {
        return $this->uploadFile($file, UploadConfig::PROVIDER_COMPLETED_WORK_DIR, 'work-' . $workId);
    }

    /**
     * Upload générique de fichier
     */
    private function uploadFile(array $file, string $directory, string $prefix): string
    {
        // Vérifications de sécurité
        $this->validateFile($file);

        // Créer le dossier s'il n'existe pas
        $uploadPath = UploadConfig::getUploadPath($directory);
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Générer un nom de fichier unique
        $originalName = $file['name'];
        $uniqueFilename = UploadConfig::generateUniqueFilename($originalName, $directory);

        // Chemin complet du fichier
        $filepath = $uploadPath . $uniqueFilename;

        // Déplacer le fichier uploadé
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new \RuntimeException('Erreur lors du déplacement du fichier uploadé');
        }

        // Retourner l'URL relative pour la base de données
        return UploadConfig::getRelativeUrl($directory, $uniqueFilename);
    }

    /**
     * Valide un fichier uploadé
     */
    private function validateFile(array $file): void
    {
        // Vérifier les erreurs d'upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Erreur lors de l\'upload: ' . $file['error']);
        }

        // Vérifier que le fichier a été uploadé via HTTP POST
        if (!is_uploaded_file($file['tmp_name'])) {
            throw new \RuntimeException('Fichier non uploadé via HTTP POST');
        }

        // Vérifier l'extension
        if (!UploadConfig::isAllowedExtension($file['name'])) {
            throw new \RuntimeException('Extension de fichier non autorisée');
        }

        // Vérifier le type MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!UploadConfig::isAllowedType($mimeType)) {
            throw new \RuntimeException('Type de fichier non autorisé: ' . $mimeType);
        }

        // Vérifier la taille
        $maxSize = strpos($mimeType, 'image/') === 0 ?
            UploadConfig::MAX_IMAGE_SIZE : UploadConfig::MAX_DOCUMENT_SIZE;

        if ($file['size'] > $maxSize) {
            throw new \RuntimeException('Fichier trop volumineux. Taille max: ' . ($maxSize / 1024 / 1024) . 'MB');
        }
    }

    /**
     * Supprime un fichier
     */
    public function deleteFile(string $fileUrl): bool
    {
        // Extraire le chemin du fichier depuis l'URL
        $path = parse_url($fileUrl, PHP_URL_PATH);
        if (!$path) {
            return false;
        }

        // Enlever le préfixe /uploads/
        $relativePath = str_replace('/uploads/', '', $path);

        // Déterminer le dossier
        $parts = explode('/', $relativePath);
        if (count($parts) < 2) {
            return false;
        }

        $directory = $parts[0] . '/';
        $filename = $parts[1];

        return UploadConfig::deleteFile($directory, $filename);
    }

    /**
     * Vérifie si un fichier existe
     */
    public function fileExists(string $fileUrl): bool
    {
        $path = parse_url($fileUrl, PHP_URL_PATH);
        if (!$path) {
            return false;
        }

        $relativePath = str_replace('/uploads/', '', $path);
        $parts = explode('/', $relativePath);

        if (count($parts) < 2) {
            return false;
        }

        $directory = $parts[0] . '/';
        $filename = $parts[1];

        $filepath = UploadConfig::getUploadPath($directory) . $filename;
        return file_exists($filepath);
    }
}
