<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Config;

class UploadConfig
{
    // Dossier racine des images
    public const IMAGES_ROOT = __DIR__ . '/../../images/';

    // Sous-dossiers par type et entité
    // Providers
    public const PROVIDER_PROFILE_DIR = 'providers/profilepicture/uploads/';
    public const PROVIDER_SERVICE_COVER_DIR = 'providers/services/cover/uploads/';
    public const PROVIDER_SERVICE_IMAGE_DIR = 'providers/services/images/uploads/';
    public const PROVIDER_ARTICLE_COVER_DIR = 'providers/articles/cover/uploads/';
    public const PROVIDER_ARTICLE_IMAGE_DIR = 'providers/articles/articleimage/uploads/';
    public const PROVIDER_EXPERIENCE_DIR = 'providers/experiences/uploads/';
    public const PROVIDER_EDUCATION_DIR = 'providers/educations/uploads/';
    public const PROVIDER_COMPLETED_WORK_DIR = 'providers/completedworks/uploads/';

    // Clients
    public const CLIENT_PROFILE_DIR = 'client/profilepictures/uploads/';

    // Types de fichiers autorisés
    public const ALLOWED_IMAGE_TYPES = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/avif',
        'image/svg+xml'
    ];

    public const ALLOWED_DOCUMENT_TYPES = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/plain'
    ];

    // Tailles maximales (en bytes)
    public const MAX_IMAGE_SIZE = 5 * 1024 * 1024; // 5MB
    public const MAX_DOCUMENT_SIZE = 10 * 1024 * 1024; // 10MB

    // Extensions autorisées
    public const ALLOWED_EXTENSIONS = [
        'jpg',
        'jpeg',
        'png',
        'gif',
        'webp',
        'avif',
        'svg',
        'pdf',
        'doc',
        'docx',
        'xls',
        'xlsx',
        'txt'
    ];

    /**
     * Génère un nom de fichier unique
     */
    public static function generateUniqueFilename(string $originalName, string $directory): string
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $basename = pathinfo($originalName, PATHINFO_FILENAME);

        // Nettoyer le nom de base
        $basename = preg_replace('/[^a-zA-Z0-9_-]/', '', $basename);
        $basename = substr($basename, 0, 50); // Limiter la longueur

        // Ajouter un timestamp et un hash pour l'unicité
        $timestamp = time();
        $hash = substr(md5(uniqid()), 0, 8);

        return $basename . '-' . $timestamp . '-' . $hash . '.' . $extension;
    }

    /**
     * Vérifie si le type de fichier est autorisé
     */
    public static function isAllowedType(string $mimeType): bool
    {
        return in_array($mimeType, self::ALLOWED_IMAGE_TYPES) ||
            in_array($mimeType, self::ALLOWED_DOCUMENT_TYPES);
    }

    /**
     * Vérifie si l'extension est autorisée
     */
    public static function isAllowedExtension(string $filename): bool
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($extension, self::ALLOWED_EXTENSIONS);
    }

    /**
     * Obtient le chemin complet pour un type de fichier
     */
    public static function getUploadPath(string $directory): string
    {
        return self::IMAGES_ROOT . $directory;
    }

    /**
     * Obtient l'URL relative pour un fichier
     */
    public static function getRelativeUrl(string $directory, string $filename): string
    {
        return '/api/v1/images/' . $directory . $filename;
    }

    /**
     * Supprime un fichier s'il existe
     */
    public static function deleteFile(string $directory, string $filename): bool
    {
        $filepath = self::getUploadPath($directory) . $filename;
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        return false;
    }

    /**
     * Obtient l'URL complète pour un fichier
     */
    public static function getFullUrl(string $directory, string $filename): string
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $protocol . '://' . $host . self::getRelativeUrl($directory, $filename);
    }
}
