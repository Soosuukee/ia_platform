<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Controller;

use Soosuuke\IaPlatform\Repository\ClientRepository;
use Soosuuke\IaPlatform\Repository\CountryRepository;
use Soosuuke\IaPlatform\Entity\Client;
use Soosuuke\IaPlatform\Service\ClientSlugificationService;
use Soosuuke\IaPlatform\Service\FileUploadService;
use Soosuuke\IaPlatform\Config\AuthMiddleware;

class ClientController
{
    private ClientRepository $clientRepository;
    private CountryRepository $countryRepository;
    private ClientSlugificationService $slugificationService;
    private FileUploadService $fileUploadService;

    public function __construct()
    {
        $this->clientRepository = new ClientRepository();
        $this->countryRepository = new CountryRepository();
        $this->slugificationService = new ClientSlugificationService();
        $this->fileUploadService = new FileUploadService();
    }

    // GET /clients
    public function getAllClients(): array
    {
        $clients = $this->clientRepository->findAll();
        return array_map(fn($c) => method_exists($c, 'toArray') ? $c->toArray() : $c, $clients);
    }

    // GET /clients/{id}
    public function getClientById(int $id): ?array
    {
        $client = $this->clientRepository->findById($id);
        return $client ? (method_exists($client, 'toArray') ? $client->toArray() : null) : null;
    }

    // GET /clients/email/{email}
    public function getClientByEmail(string $email): ?array
    {
        $client = $this->clientRepository->findByEmail($email);
        return $client ? (method_exists($client, 'toArray') ? $client->toArray() : null) : null;
    }

    // GET /clients/slug/{slug}
    public function getClientBySlug(string $slug): ?array
    {
        $client = $this->clientRepository->findBySlug($slug);
        return $client ? (method_exists($client, 'toArray') ? $client->toArray() : null) : null;
    }

    // POST /clients
    public function createClient(array $data): Client
    {
        // Générer le slug automatiquement
        $slug = $this->slugificationService->generateClientSlug(
            $data['firstName'],
            $data['lastName'],
            function ($slug) {
                return $this->clientRepository->findBySlug($slug) !== null;
            }
        );

        $client = new Client(
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

        $this->clientRepository->save($client);
        return $client;
    }

    // PUT /clients/{id}
    public function updateClient(int $id, array $data): ?Client
    {
        $client = $this->clientRepository->findById($id);
        if (!$client) {
            return null;
        }

        // Security: only owner client can update own profile
        $currentUserId = AuthMiddleware::getCurrentUserId();
        $currentUserType = AuthMiddleware::getCurrentUserType();
        if ($currentUserType !== 'client' || $client->getId() !== $currentUserId) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Accès interdit']);
            return null;
        }

        // Mise à jour des propriétés
        $client = new Client(
            $data['firstName'] ?? $client->getFirstName(),
            $data['lastName'] ?? $client->getLastName(),
            $data['email'] ?? $client->getEmail(),
            $data['countryId'] ?? $client->getCountryId(),
            $data['city'] ?? $client->getCity(),
            $data['profilePicture'] ?? $client->getProfilePicture(),
            $data['slug'] ?? $client->getSlug(),
            $data['state'] ?? $client->getState(),
            $data['postalCode'] ?? $client->getPostalCode(),
            $data['address'] ?? $client->getAddress()
        );

        $this->clientRepository->update($client);
        return $client;
    }

    // DELETE /clients/{id}
    public function deleteClient(int $id): bool
    {
        $client = $this->clientRepository->findById($id);
        if (!$client) {
            return false;
        }

        $this->clientRepository->delete($id);
        return true;
    }

    // GET /countries
    public function getAllCountries(): array
    {
        return $this->countryRepository->findAll();
    }

    // POST /clients/{id}/profile-picture
    public function uploadProfilePicture(int $clientId, array $file): array
    {
        try {
            // Security: only owner client can update own profile picture
            $currentUserId = AuthMiddleware::getCurrentUserId();
            $currentUserType = AuthMiddleware::getCurrentUserType();
            if ($currentUserType !== 'client' || $clientId !== $currentUserId) {
                return [
                    'success' => false,
                    'message' => 'Accès interdit'
                ];
            }
            $client = $this->clientRepository->findById($clientId);
            if (!$client) {
                return [
                    'success' => false,
                    'message' => 'Client non trouvé'
                ];
            }

            if ($client->getProfilePicture()) {
                $this->fileUploadService->deleteFile($client->getProfilePicture());
            }

            $newProfilePictureUrl = $this->fileUploadService->uploadClientProfilePicture(
                $file,
                $clientId
            );

            $client->setProfilePicture($newProfilePictureUrl);
            $this->clientRepository->update($client);

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
}
