<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Controller;

use Soosuuke\IaPlatform\Repository\ProviderRepository;
use Soosuuke\IaPlatform\Repository\ClientRepository;
use Soosuuke\IaPlatform\Entity\Provider;
use Soosuuke\IaPlatform\Entity\Client;

class AuthController
{
    private ProviderRepository $providerRepository;
    private ClientRepository $clientRepository;

    public function __construct()
    {
        $this->providerRepository = new ProviderRepository();
        $this->clientRepository = new ClientRepository();
    }

    // POST /auth/login
    public function login(array $data): array
    {
        try {
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';
            $userType = $data['userType'] ?? 'provider'; // 'provider' ou 'client'

            if (empty($email) || empty($password)) {
                return [
                    'success' => false,
                    'message' => 'Email et mot de passe requis'
                ];
            }

            // Chercher l'utilisateur selon le type
            if ($userType === 'provider') {
                $user = $this->providerRepository->findByEmail($email);
            } else {
                $user = $this->clientRepository->findByEmail($email);
            }

            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Utilisateur non trouvé'
                ];
            }

            // Vérifier le mot de passe
            if (!password_verify($password, $user->getPassword())) {
                return [
                    'success' => false,
                    'message' => 'Mot de passe incorrect'
                ];
            }

            // Démarrer la session
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Stocker les informations de session
            $_SESSION['user_id'] = $user->getId();
            $_SESSION['user_type'] = $userType;
            $_SESSION['user_email'] = $user->getEmail();
            $_SESSION['user_name'] = $user->getFirstName() . ' ' . $user->getLastName();

            return [
                'success' => true,
                'message' => 'Connexion réussie',
                'user' => [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'firstName' => $user->getFirstName(),
                    'lastName' => $user->getLastName(),
                    'type' => $userType,
                    'slug' => $user->getSlug()
                ]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de la connexion: ' . $e->getMessage()
            ];
        }
    }

    // POST /auth/logout
    public function logout(): array
    {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Détruire la session
            session_destroy();

            return [
                'success' => true,
                'message' => 'Déconnexion réussie'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de la déconnexion: ' . $e->getMessage()
            ];
        }
    }

    // GET /auth/me
    public function getCurrentUser(): array
    {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
                return [
                    'success' => false,
                    'message' => 'Utilisateur non connecté'
                ];
            }

            $userId = $_SESSION['user_id'];
            $userType = $_SESSION['user_type'];

            // Récupérer les informations utilisateur
            if ($userType === 'provider') {
                $user = $this->providerRepository->findById($userId);
            } else {
                $user = $this->clientRepository->findById($userId);
            }

            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Utilisateur non trouvé'
                ];
            }

            return [
                'success' => true,
                'user' => [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'firstName' => $user->getFirstName(),
                    'lastName' => $user->getLastName(),
                    'type' => $userType,
                    'slug' => $user->getSlug(),
                    'profilePicture' => $user->getProfilePicture()
                ]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de la récupération: ' . $e->getMessage()
            ];
        }
    }

    // POST /auth/register
    public function register(array $data): array
    {
        try {
            $userType = $data['userType'] ?? 'provider';
            $firstName = $data['firstName'] ?? '';
            $lastName = $data['lastName'] ?? '';
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';
            $countryId = $data['countryId'] ?? 1;
            $city = $data['city'] ?? '';

            // Validation des données
            if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
                return [
                    'success' => false,
                    'message' => 'Tous les champs sont requis'
                ];
            }

            // Vérifier si l'email existe déjà
            if ($userType === 'provider') {
                $existingUser = $this->providerRepository->findByEmail($email);
            } else {
                $existingUser = $this->clientRepository->findByEmail($email);
            }

            if ($existingUser) {
                return [
                    'success' => false,
                    'message' => 'Cet email est déjà utilisé'
                ];
            }

            // Hasher le mot de passe
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Créer l'utilisateur
            if ($userType === 'provider') {
                $user = new Provider(
                    $firstName,
                    $lastName,
                    $email,
                    $hashedPassword,
                    $countryId,
                    $city
                );
                $this->providerRepository->save($user);
            } else {
                $user = new Client(
                    $firstName,
                    $lastName,
                    $email,
                    $hashedPassword,
                    $countryId,
                    $city
                );
                $this->clientRepository->save($user);
            }

            // Connecter automatiquement l'utilisateur
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $_SESSION['user_id'] = $user->getId();
            $_SESSION['user_type'] = $userType;
            $_SESSION['user_email'] = $user->getEmail();
            $_SESSION['user_name'] = $user->getFirstName() . ' ' . $user->getLastName();

            return [
                'success' => true,
                'message' => 'Inscription réussie',
                'user' => [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'firstName' => $user->getFirstName(),
                    'lastName' => $user->getLastName(),
                    'type' => $userType,
                    'slug' => $user->getSlug()
                ]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'inscription: ' . $e->getMessage()
            ];
        }
    }

    // POST /auth/change-password
    public function changePassword(array $data): array
    {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
                return [
                    'success' => false,
                    'message' => 'Utilisateur non connecté'
                ];
            }

            $currentPassword = $data['currentPassword'] ?? '';
            $newPassword = $data['newPassword'] ?? '';

            if (empty($currentPassword) || empty($newPassword)) {
                return [
                    'success' => false,
                    'message' => 'Ancien et nouveau mot de passe requis'
                ];
            }

            $userId = $_SESSION['user_id'];
            $userType = $_SESSION['user_type'];

            // Récupérer l'utilisateur
            if ($userType === 'provider') {
                $user = $this->providerRepository->findById($userId);
            } else {
                $user = $this->clientRepository->findById($userId);
            }

            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Utilisateur non trouvé'
                ];
            }

            // Vérifier l'ancien mot de passe
            if (!password_verify($currentPassword, $user->getPassword())) {
                return [
                    'success' => false,
                    'message' => 'Ancien mot de passe incorrect'
                ];
            }

            // Hasher et mettre à jour le nouveau mot de passe
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Créer un nouvel objet avec le nouveau mot de passe
            if ($userType === 'provider') {
                $updatedUser = new Provider(
                    $user->getFirstName(),
                    $user->getLastName(),
                    $user->getEmail(),
                    $hashedPassword,
                    $user->getCountryId(),
                    $user->getCity(),
                    $user->getProfilePicture(),
                    $user->getSlug()
                );
                $this->providerRepository->update($updatedUser);
            } else {
                $updatedUser = new Client(
                    $user->getFirstName(),
                    $user->getLastName(),
                    $user->getEmail(),
                    $hashedPassword,
                    $user->getCountryId(),
                    $user->getCity(),
                    $user->getProfilePicture(),
                    $user->getSlug()
                );
                $this->clientRepository->update($updatedUser);
            }

            return [
                'success' => true,
                'message' => 'Mot de passe modifié avec succès'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors du changement de mot de passe: ' . $e->getMessage()
            ];
        }
    }
}
