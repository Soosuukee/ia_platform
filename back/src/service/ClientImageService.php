<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Service;

class ClientImageService
{
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'avif'];
    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/webp',
        'image/avif'
    ];

    public function createClientImageStructure(int $clientId, string $profilePicture): void
    {
        $baseDir = __DIR__ . '/../../images/clients/' . $clientId;

        $profileDir = $baseDir . '/profile';
        if (!is_dir($profileDir)) {
            mkdir($profileDir, 0755, true);
        }

        $sourceProfile = __DIR__ . '/../../fixtures_images/client/profilepictures/' . $profilePicture;
        $destProfile = $profileDir . '/' . $profilePicture;

        if (file_exists($sourceProfile)) {
            copy($sourceProfile, $destProfile);
            echo "  📸 Image de profil client copiée : $profilePicture\n";
        }
    }

    /**
     * Upload une image de profil pour un client
     */
    public function uploadClientProfileImage(
        int $clientId,
        string $tempFilePath,
        string $originalFilename,
        bool $replaceExisting = false
    ): array {
        try {
            // Validation du fichier
            $validationResult = $this->validateUploadedFile($tempFilePath, $originalFilename);
            if (!$validationResult['success']) {
                return $validationResult;
            }

            // Créer la structure de dossiers
            $baseDir = $this->getClientImageBaseDir($clientId);
            $this->ensureDirectoryExists($baseDir);

            // Déterminer le chemin de destination
            $destinationPath = $baseDir . '/profile/' . $originalFilename;

            // Gérer les collisions de noms
            if (!$replaceExisting && file_exists($destinationPath)) {
                $destinationPath = $this->generateUniqueFilename($destinationPath);
            }

            // Créer le dossier de destination si nécessaire
            $this->ensureDirectoryExists(dirname($destinationPath));

            // Déplacer le fichier
            if (!move_uploaded_file($tempFilePath, $destinationPath)) {
                return [
                    'success' => false,
                    'message' => 'Erreur lors du déplacement du fichier',
                    'error' => 'MOVE_FAILED'
                ];
            }

            // Retourner le succès avec les informations du fichier
            return [
                'success' => true,
                'message' => 'Image de profil uploadée avec succès',
                'data' => [
                    'filename' => basename($destinationPath),
                    'path' => $destinationPath,
                    'size' => filesize($destinationPath),
                    'mime_type' => mime_content_type($destinationPath)
                ]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'upload: ' . $e->getMessage(),
                'error' => 'UPLOAD_EXCEPTION'
            ];
        }
    }

    /**
     * Supprimer une image de profil de client
     */
    public function deleteClientProfileImage(int $clientId, string $filename): array
    {
        try {
            $filePath = $this->getClientImageBaseDir($clientId) . '/profile/' . $filename;

            if (!file_exists($filePath)) {
                return [
                    'success' => false,
                    'message' => 'Fichier non trouvé',
                    'error' => 'FILE_NOT_FOUND'
                ];
            }

            if (!unlink($filePath)) {
                return [
                    'success' => false,
                    'message' => 'Erreur lors de la suppression du fichier',
                    'error' => 'DELETE_FAILED'
                ];
            }

            return [
                'success' => true,
                'message' => 'Image de profil supprimée avec succès'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage(),
                'error' => 'DELETE_EXCEPTION'
            ];
        }
    }

    /**
     * Récupérer la liste des images de profil d'un client
     */
    public function getClientProfileImages(int $clientId): array
    {
        try {
            $profileDir = $this->getClientImageBaseDir($clientId) . '/profile';

            if (!is_dir($profileDir)) {
                return [
                    'success' => true,
                    'data' => []
                ];
            }

            $images = [];
            $files = scandir($profileDir);

            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && is_file($profileDir . '/' . $file)) {
                    $filePath = $profileDir . '/' . $file;
                    $images[] = [
                        'filename' => $file,
                        'path' => $filePath,
                        'size' => filesize($filePath),
                        'mime_type' => mime_content_type($filePath),
                        'upload_date' => filemtime($filePath)
                    ];
                }
            }

            return [
                'success' => true,
                'data' => $images
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de la récupération des images: ' . $e->getMessage(),
                'error' => 'LIST_EXCEPTION'
            ];
        }
    }

    /**
     * Valider un fichier uploadé
     */
    private function validateUploadedFile(string $tempFilePath, string $originalFilename): array
    {
        // Vérifier que le fichier existe
        if (!file_exists($tempFilePath)) {
            return [
                'success' => false,
                'message' => 'Fichier temporaire non trouvé',
                'error' => 'TEMP_FILE_NOT_FOUND'
            ];
        }

        // Vérifier la taille
        if (filesize($tempFilePath) > self::MAX_FILE_SIZE) {
            return [
                'success' => false,
                'message' => 'Fichier trop volumineux (max 5MB)',
                'error' => 'FILE_TOO_LARGE'
            ];
        }

        // Vérifier l'extension
        $extension = strtolower(pathinfo($originalFilename, PATHINFO_EXTENSION));
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            return [
                'success' => false,
                'message' => 'Extension de fichier non autorisée',
                'error' => 'INVALID_EXTENSION'
            ];
        }

        // Vérifier le type MIME
        $mimeType = mime_content_type($tempFilePath);
        if (!in_array($mimeType, self::ALLOWED_MIME_TYPES)) {
            return [
                'success' => false,
                'message' => 'Type de fichier non autorisé',
                'error' => 'INVALID_MIME_TYPE'
            ];
        }

        return ['success' => true];
    }

    /**
     * Obtenir le répertoire de base pour les images du client
     */
    private function getClientImageBaseDir(int $clientId): string
    {
        return __DIR__ . '/../../images/clients/' . $clientId;
    }

    /**
     * S'assurer qu'un répertoire existe
     */
    private function ensureDirectoryExists(string $directory): void
    {
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }

    /**
     * Générer un nom de fichier unique
     */
    private function generateUniqueFilename(string $originalPath): string
    {
        $directory = dirname($originalPath);
        $filename = pathinfo($originalPath, PATHINFO_FILENAME);
        $extension = pathinfo($originalPath, PATHINFO_EXTENSION);
        $counter = 1;

        do {
            $newPath = $directory . '/' . $filename . '_' . $counter . '.' . $extension;
            $counter++;
        } while (file_exists($newPath));

        return $newPath;
    }
}
