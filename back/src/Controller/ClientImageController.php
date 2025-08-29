<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Controller;

use Soosuuke\IaPlatform\Service\ClientImageService;

class ClientImageController
{
    private ClientImageService $imageService;

    public function __construct()
    {
        $this->imageService = new ClientImageService();
    }

    /**
     * Upload d'une image de profil
     */
    public function uploadProfileImage(int $clientId): array
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

        return $this->imageService->uploadClientProfileImage(
            $clientId,
            $file['tmp_name'],
            $file['name'],
            true // Remplacer l'image existante
        );
    }

    /**
     * Supprimer une image de profil
     */
    public function deleteProfileImage(int $clientId, string $filename): array
    {
        return $this->imageService->deleteClientProfileImage($clientId, $filename);
    }

    /**
     * Lister les images de profil d'un client
     */
    public function listProfileImages(int $clientId): array
    {
        return $this->imageService->getClientProfileImages($clientId);
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
