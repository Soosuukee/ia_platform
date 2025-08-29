<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Service;

class ProviderImageService
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

    /**
     * Crée la structure minimale d'un provider et place éventuellement une image de profil
     */
    public function createProviderImageStructure(int $providerId, string $profilePicture): ?string
    {
        $baseDir = __DIR__ . '/../../images/providers/' . $providerId;

        // Créer les dossiers principaux
        $directories = [
            $baseDir . '/profile',
            $baseDir . '/services',
            $baseDir . '/articles',
            $baseDir . '/experiences',
            $baseDir . '/education'
        ];

        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }

        // Copier l'image de profil en la renommant en profile-picture.{ext}
        $sourceProfile = __DIR__ . '/../../fixtures_images/providers/profilepicture/' . $profilePicture;
        $publicProfileUrl = null;
        if (file_exists($sourceProfile)) {
            $extension = strtolower(pathinfo($profilePicture, PATHINFO_EXTENSION));
            $destProfile = $baseDir . '/profile/profile-picture.' . $extension;
            copy($sourceProfile, $destProfile);
            $publicProfileUrl = '/api/v1/images/providers/' . $providerId . '/profile/profile-picture.' . $extension;
            echo "  📸 Image de profil copiée : profile-picture.$extension\n";
        }

        // Copier quelques images de services (exemple)
        $serviceImages = [
            'cover1.jpg',
            'cover2.jpg'
        ];

        foreach ($serviceImages as $index => $image) {
            $sourceService = __DIR__ . '/../../fixtures_images/providers/services/' . $image;
            $destService = $baseDir . '/services/' . ($index + 1) . '/' . $image;

            if (file_exists($sourceService)) {
                // Créer le sous-dossier pour le service
                $serviceDir = dirname($destService);
                if (!is_dir($serviceDir)) {
                    mkdir($serviceDir, 0755, true);
                }
                copy($sourceService, $destService);
                echo "  🛠️ Image de service copiée : $image\n";
            }
        }

        // Copier quelques images d'articles (exemple)
        $articleImages = [
            'article1.jpg',
            'article2.jpg'
        ];

        foreach ($articleImages as $index => $image) {
            $sourceArticle = __DIR__ . '/../../fixtures_images/providers/articles/' . $image;
            $destArticle = $baseDir . '/articles/' . ($index + 1) . '/' . $image;

            if (file_exists($sourceArticle)) {
                // Créer le sous-dossier pour l'article
                $articleDir = dirname($destArticle);
                if (!is_dir($articleDir)) {
                    mkdir($articleDir, 0755, true);
                }
                copy($sourceArticle, $destArticle);
                echo "  📄 Image d'article copiée : $image\n";
            }
        }

        // Copier quelques images d'expériences (exemple)
        $experienceImages = [
            'exp1.jpg',
            'exp2.jpg'
        ];

        foreach ($experienceImages as $index => $image) {
            $sourceExp = __DIR__ . '/../../fixtures_images/providers/experiences/' . $image;
            $destExp = $baseDir . '/experiences/' . ($index + 1) . '/' . $image;

            if (file_exists($sourceExp)) {
                // Créer le sous-dossier pour l'expérience
                $expDir = dirname($destExp);
                if (!is_dir($expDir)) {
                    mkdir($expDir, 0755, true);
                }
                copy($sourceExp, $destExp);
                echo "  💼 Image d'expérience copiée : $image\n";
            }
        }

        // Copier quelques images d'éducation (exemple)
        $educationImages = [
            'university1.jpg',
            'diploma1.jpg'
        ];

        foreach ($educationImages as $index => $image) {
            $sourceEdu = __DIR__ . '/../../fixtures_images/providers/educations/' . $image;
            $destEdu = $baseDir . '/education/' . ($index + 1) . '/' . $image;

            if (file_exists($sourceEdu)) {
                // Créer le sous-dossier pour l'éducation
                $eduDir = dirname($destEdu);
                if (!is_dir($eduDir)) {
                    mkdir($eduDir, 0755, true);
                }
                copy($sourceEdu, $destEdu);
                echo "  🎓 Image d'éducation copiée : $image\n";
            }
        }
        return $publicProfileUrl;
    }

    /**
     * Assure la structure de base d'un provider
     */
    public function ensureProviderStructure(int $providerId): void
    {
        $baseDir = $this->getProviderImageBaseDir($providerId);
        $directories = [
            $baseDir,
            $baseDir . '/profile',
            $baseDir . '/services',
            $baseDir . '/articles',
            $baseDir . '/experiences',
            $baseDir . '/education',
        ];
        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }

    /**
     * Assure la structure d'un service donné (incluant dossier de service et arborescence sections/contents si fournie)
     */
    public function ensureServiceStructure(int $providerId, int $serviceId, ?int $sectionId = null, ?int $contentId = null): void
    {
        $this->ensureProviderStructure($providerId);
        $serviceDir = $this->getProviderImageBaseDir($providerId) . '/services/' . $serviceId;
        $this->ensureDirectoryExists($serviceDir);

        // Dossier cover à la racine du service (le fichier cover.* sera placé directement dans $serviceDir)
        if ($sectionId !== null && $contentId !== null) {
            $contentDir = $serviceDir . '/sections/' . $sectionId . '/contents/' . $contentId;
            $this->ensureDirectoryExists($contentDir);
        }
    }

    /**
     * Assure la structure pour article/experience/education: un dossier par ID, images directement dedans
     */
    public function ensureEntityStructure(int $providerId, string $entityType, int $entityId): void
    {
        $this->ensureProviderStructure($providerId);
        $dir = $this->getProviderImageBaseDir($providerId) . '/' . $entityType . '/' . $entityId;
        $this->ensureDirectoryExists($dir);
    }

    /**
     * Upload de la cover d'un service → {base}/services/{serviceId}/cover.ext
     */
    public function uploadServiceCover(int $providerId, int $serviceId, string $tempFilePath, string $originalFilename, bool $replaceExisting = true): array
    {
        $this->ensureServiceStructure($providerId, $serviceId);

        $validation = $this->validateUploadedFile($tempFilePath, $originalFilename);
        if (!$validation['success']) {
            return $validation;
        }

        $extension = strtolower(pathinfo($originalFilename, PATHINFO_EXTENSION));
        $destinationDir = $this->getProviderImageBaseDir($providerId) . '/services/' . $serviceId . '/cover';
        $this->ensureDirectoryExists($destinationDir);
        $destinationPath = $destinationDir . '/service-cover.' . $extension;

        if (!$replaceExisting && file_exists($destinationPath)) {
            $destinationPath = $this->generateUniqueFilename($destinationPath);
        }

        if (!move_uploaded_file($tempFilePath, $destinationPath)) {
            return ['success' => false, 'message' => 'Erreur lors du déplacement du fichier', 'error' => 'MOVE_FAILED'];
        }

        return [
            'success' => true,
            'message' => 'Cover uploadée',
            'data' => [
                'filename' => basename($destinationPath),
                'path' => $destinationPath,
                'size' => filesize($destinationPath),
                'mime_type' => mime_content_type($destinationPath),
                'public_url' => '/api/v1/images/providers/' . $providerId . '/services/' . $serviceId . '/cover/' . basename($destinationPath)
            ]
        ];
    }

    /**
     * Upload d'une image de contenu de service → {base}/services/{serviceId}/sections/{sectionId}/contents/{contentId}/
     */
    public function uploadServiceContentImage(
        int $providerId,
        int $serviceId,
        int $sectionId,
        int $contentId,
        string $tempFilePath,
        string $originalFilename,
        bool $replaceExisting = false
    ): array {
        $this->ensureServiceStructure($providerId, $serviceId, $sectionId, $contentId);

        $validation = $this->validateUploadedFile($tempFilePath, $originalFilename);
        if (!$validation['success']) {
            return $validation;
        }

        $destinationDir = $this->getProviderImageBaseDir($providerId)
            . '/services/' . $serviceId . '/sections/' . $sectionId . '/contents/' . $contentId;
        $this->ensureDirectoryExists($destinationDir);

        $extension = strtolower(pathinfo($originalFilename, PATHINFO_EXTENSION));
        $nextIndex = $this->getNextSequentialIndex($destinationDir, 'content-image-', $extension);
        $destinationPath = $destinationDir . '/content-image-' . $nextIndex . '.' . $extension;

        if (!$replaceExisting && file_exists($destinationPath)) {
            $destinationPath = $this->generateUniqueFilename($destinationPath);
        }

        if (!move_uploaded_file($tempFilePath, $destinationPath)) {
            return ['success' => false, 'message' => 'Erreur lors du déplacement du fichier', 'error' => 'MOVE_FAILED'];
        }

        return [
            'success' => true,
            'message' => 'Image de contenu uploadée',
            'data' => [
                'filename' => basename($destinationPath),
                'path' => $destinationPath,
                'size' => filesize($destinationPath),
                'mime_type' => mime_content_type($destinationPath)
            ]
        ];
    }

    /**
     * Upload d'une image pour article/experience/education → {base}/{type}/{id}/
     */
    public function uploadEntityImage(
        int $providerId,
        string $entityType, // 'articles' | 'experiences' | 'education'
        int $entityId,
        string $tempFilePath,
        string $originalFilename,
        bool $replaceExisting = false
    ): array {
        if (!in_array($entityType, ['articles', 'experiences', 'education'], true)) {
            return ['success' => false, 'message' => 'Type d\'entité non autorisé', 'error' => 'INVALID_ENTITY_TYPE'];
        }

        $this->ensureEntityStructure($providerId, $entityType, $entityId);

        $validation = $this->validateUploadedFile($tempFilePath, $originalFilename);
        if (!$validation['success']) {
            return $validation;
        }

        $destinationDir = $this->getProviderImageBaseDir($providerId) . '/' . $entityType . '/' . $entityId;
        $this->ensureDirectoryExists($destinationDir);

        $extension = strtolower(pathinfo($originalFilename, PATHINFO_EXTENSION));
        if ($entityType === 'experiences') {
            $destinationPath = $destinationDir . '/exp' . $entityId . '.' . $extension;
        } elseif ($entityType === 'articles') {
            $index = $this->getNextSequentialIndex($destinationDir, 'article-image-', $extension);
            $destinationPath = $destinationDir . '/article-image-' . $index . '.' . $extension;
        } else { // education
            $index = $this->getNextSequentialIndex($destinationDir, 'education-image-', $extension);
            $destinationPath = $destinationDir . '/education-image-' . $index . '.' . $extension;
        }

        if (!$replaceExisting && file_exists($destinationPath)) {
            $destinationPath = $this->generateUniqueFilename($destinationPath);
        }

        if (!move_uploaded_file($tempFilePath, $destinationPath)) {
            return ['success' => false, 'message' => 'Erreur lors du déplacement du fichier', 'error' => 'MOVE_FAILED'];
        }

        return [
            'success' => true,
            'message' => 'Image uploadée',
            'data' => [
                'filename' => basename($destinationPath),
                'path' => $destinationPath,
                'size' => filesize($destinationPath),
                'mime_type' => mime_content_type($destinationPath)
            ]
        ];
    }

    /**
     * Upload une image pour un provider
     */
    public function uploadProviderImage(
        int $providerId,
        string $imageType,
        string $tempFilePath,
        string $originalFilename,
        ?int $subId = null,
        bool $replaceExisting = false
    ): array {
        try {
            // Validation du type d'image
            if (!in_array($imageType, ['profile', 'services', 'articles', 'experiences', 'education'])) {
                return [
                    'success' => false,
                    'message' => 'Type d\'image non autorisé',
                    'error' => 'INVALID_IMAGE_TYPE'
                ];
            }

            // Validation du fichier
            $validationResult = $this->validateUploadedFile($tempFilePath, $originalFilename);
            if (!$validationResult['success']) {
                return $validationResult;
            }

            // Créer la structure de base du provider
            $baseDir = $this->getProviderImageBaseDir($providerId);
            $this->ensureDirectoryExists($baseDir);

            $extension = strtolower(pathinfo($originalFilename, PATHINFO_EXTENSION));
            $destinationDir = $this->buildTypeDirectory($baseDir, $imageType, $subId);
            $this->ensureDirectoryExists($destinationDir);

            // Déterminer le nom de fichier selon le type
            switch ($imageType) {
                case 'profile':
                    $filename = 'profile-picture.' . $extension;
                    break;
                case 'experiences':
                    if ($subId === null) {
                        return ['success' => false, 'message' => 'experienceId manquant', 'error' => 'MISSING_SUB_ID'];
                    }
                    $filename = 'exp' . $subId . '.' . $extension;
                    break;
                case 'services':
                    if ($subId === null) {
                        return ['success' => false, 'message' => 'serviceId manquant', 'error' => 'MISSING_SUB_ID'];
                    }
                    $next = $this->getNextSequentialIndex($destinationDir, 'service-image-', $extension);
                    $filename = 'service-image-' . $next . '.' . $extension;
                    break;
                case 'articles':
                    if ($subId === null) {
                        return ['success' => false, 'message' => 'articleId manquant', 'error' => 'MISSING_SUB_ID'];
                    }
                    $next = $this->getNextSequentialIndex($destinationDir, 'article-image-', $extension);
                    $filename = 'article-image-' . $next . '.' . $extension;
                    break;
                case 'education':
                    if ($subId === null) {
                        return ['success' => false, 'message' => 'educationId manquant', 'error' => 'MISSING_SUB_ID'];
                    }
                    $next = $this->getNextSequentialIndex($destinationDir, 'education-image-', $extension);
                    $filename = 'education-image-' . $next . '.' . $extension;
                    break;
                default:
                    $filename = basename($originalFilename);
            }

            $destinationPath = $destinationDir . '/' . $filename;

            if (!$replaceExisting && file_exists($destinationPath)) {
                $destinationPath = $this->generateUniqueFilename($destinationPath);
            }

            if (!move_uploaded_file($tempFilePath, $destinationPath)) {
                return [
                    'success' => false,
                    'message' => 'Erreur lors du déplacement du fichier',
                    'error' => 'MOVE_FAILED'
                ];
            }

            return [
                'success' => true,
                'message' => 'Image uploadée avec succès',
                'data' => [
                    'filename' => basename($destinationPath),
                    'path' => $destinationPath,
                    'size' => filesize($destinationPath),
                    'mime_type' => mime_content_type($destinationPath),
                    'public_url' => $this->buildPublicUrl($providerId, $imageType, basename($destinationPath), $subId)
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
     * Supprimer une image de provider
     */
    public function deleteProviderImage(int $providerId, string $imageType, string $filename, ?int $subId = null): array
    {
        try {
            $filePath = $this->buildDestinationPath($providerId, $imageType, $filename, $subId);

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
                'message' => 'Image supprimée avec succès'
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
     * Récupérer la liste des images d'un provider
     */
    public function getProviderImages(int $providerId, string $imageType, ?int $subId = null): array
    {
        try {
            $baseDir = $this->getProviderImageBaseDir($providerId);
            $typeDir = $this->buildTypeDirectory($baseDir, $imageType, $subId);

            if (!is_dir($typeDir)) {
                return [
                    'success' => true,
                    'data' => []
                ];
            }

            $images = [];
            $files = scandir($typeDir);

            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && is_file($typeDir . '/' . $file)) {
                    $filePath = $typeDir . '/' . $file;
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
     * Obtenir le répertoire de base pour les images du provider
     */
    private function getProviderImageBaseDir(int $providerId): string
    {
        return __DIR__ . '/../../images/providers/' . $providerId;
    }

    /**
     * Construire le chemin de destination
     */
    private function buildDestinationPath(int $providerId, string $imageType, string $filename, ?int $subId = null): string
    {
        $baseDir = $this->getProviderImageBaseDir($providerId);
        // Services: si aucun section/content n'est précisé via API legacy, ranger à la racine du service
        if ($imageType === 'services' && $subId !== null) {
            return $baseDir . '/services/' . $subId . '/' . $filename;
        }
        return $this->buildTypeDirectory($baseDir, $imageType, $subId) . '/' . $filename;
    }

    /**
     * Construire le répertoire selon le type d'image
     */
    private function buildTypeDirectory(string $baseDir, string $imageType, ?int $subId = null): string
    {
        $typeDir = $baseDir . '/' . $imageType;

        if ($subId !== null && in_array($imageType, ['services', 'articles', 'experiences', 'education'])) {
            $typeDir .= '/' . $subId;
        }

        return $typeDir;
    }

    private function buildPublicUrl(int $providerId, string $imageType, string $filename, ?int $subId = null): string
    {
        $base = '/api/v1/images/providers/' . $providerId . '/' . $imageType;
        if ($subId !== null && in_array($imageType, ['services', 'articles', 'experiences', 'education'])) {
            return $base . '/' . $subId . '/' . $filename;
        }
        return $base . '/' . $filename;
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

    /**
     * Trouver le prochain index séquentiel pour un préfixe donné dans un dossier
     */
    private function getNextSequentialIndex(string $directory, string $prefix, string $extension): int
    {
        $this->ensureDirectoryExists($directory);
        $files = is_dir($directory) ? scandir($directory) : [];
        $max = 0;
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            if (preg_match('/^' . preg_quote($prefix, '/') . '(\d+)\.' . preg_quote($extension, '/') . '$/i', $file, $m)) {
                $num = (int)$m[1];
                if ($num > $max) {
                    $max = $num;
                }
            }
        }
        return $max + 1;
    }

    /**
     * Copier un logo d'expérience depuis fixtures_images vers l'arborescence finale
     */
    public function copyFixtureExperienceLogo(int $providerId, int $experienceId, string $fixtureFilename): ?string
    {
        $source = __DIR__ . '/../../fixtures_images/providers/experiences/' . $fixtureFilename;
        if (!file_exists($source)) {
            return null;
        }

        $extension = strtolower(pathinfo($fixtureFilename, PATHINFO_EXTENSION));
        $destinationDir = $this->getProviderImageBaseDir($providerId) . '/experiences/' . $experienceId;
        $this->ensureDirectoryExists($destinationDir);
        $destinationPath = $destinationDir . '/exp' . $experienceId . '.' . $extension;

        copy($source, $destinationPath);
        return '/api/v1/images/providers/' . $providerId . '/experiences/' . $experienceId . '/exp' . $experienceId . '.' . $extension;
    }

    /**
     * Copier un logo d'éducation depuis fixtures_images vers l'arborescence finale
     */
    public function copyFixtureEducationLogo(int $providerId, int $educationId, string $fixtureFilename): ?string
    {
        $source = __DIR__ . '/../../fixtures_images/providers/educations/' . $fixtureFilename;
        if (!file_exists($source)) {
            return null;
        }

        $extension = strtolower(pathinfo($fixtureFilename, PATHINFO_EXTENSION));
        $destinationDir = $this->getProviderImageBaseDir($providerId) . '/education/' . $educationId;
        $this->ensureDirectoryExists($destinationDir);
        $destinationPath = $destinationDir . '/education-image-1.' . $extension;

        copy($source, $destinationPath);
        return '/api/v1/images/providers/' . $providerId . '/education/' . $educationId . '/education-image-1.' . $extension;
    }

    /**
     * Copier une image de couverture d'article depuis fixtures_images vers l'arborescence finale
     */
    public function copyFixtureArticleImage(int $providerId, int $articleId, string $fixtureFilename): ?string
    {
        $source = __DIR__ . '/../../fixtures_images/providers/articles/' . $fixtureFilename;
        if (!file_exists($source)) {
            return null;
        }

        $extension = strtolower(pathinfo($fixtureFilename, PATHINFO_EXTENSION));
        $destinationDir = $this->getProviderImageBaseDir($providerId) . '/articles/' . $articleId;
        $this->ensureDirectoryExists($destinationDir);
        $destinationPath = $destinationDir . '/article-image-1.' . $extension;

        copy($source, $destinationPath);
        return '/api/v1/images/providers/' . $providerId . '/articles/' . $articleId . '/article-image-1.' . $extension;
    }

    /**
     * Copier une cover de service depuis fixtures_images vers l'arborescence finale
     */
    public function copyFixtureServiceCover(int $providerId, int $serviceId, string $fixtureFilename): ?string
    {
        $source = __DIR__ . '/../../fixtures_images/providers/services/' . $fixtureFilename;
        if (!file_exists($source)) {
            return null;
        }

        $extension = strtolower(pathinfo($fixtureFilename, PATHINFO_EXTENSION));
        $destinationDir = $this->getProviderImageBaseDir($providerId) . '/services/' . $serviceId . '/cover';
        $this->ensureDirectoryExists($destinationDir);
        $destinationPath = $destinationDir . '/service-cover.' . $extension;

        copy($source, $destinationPath);
        return '/api/v1/images/providers/' . $providerId . '/services/' . $serviceId . '/cover/service-cover.' . $extension;
    }
}
