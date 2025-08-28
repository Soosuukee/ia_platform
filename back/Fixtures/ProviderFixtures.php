<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Fixtures;

use Soosuuke\IaPlatform\Config\Database;
use Soosuuke\IaPlatform\Entity\Provider;
use Soosuuke\IaPlatform\Repository\ProviderRepository;
use Soosuuke\IaPlatform\Service\ProviderSlugificationService;

class ProviderFixtures
{
    private \PDO $pdo;
    private ProviderRepository $providerRepository;
    private ProviderSlugificationService $slugificationService;

    public function __construct()
    {
        $this->pdo = Database::connect();
        $this->providerRepository = new ProviderRepository();
        $this->slugificationService = new ProviderSlugificationService();
    }

    public function load(): void
    {
        echo "Chargement des fixtures Provider...\n";

        // Données des providers depuis le JSON
        $providersData = [
            [
                'firstName' => 'Jensen',
                'lastName' => 'Huang',
                'email' => 'jensen.huang@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'countryId' => 9, // Taïwan
                'city' => 'Taipei',
                'profilePicture' => '/uploads/pfp/provider-1-avatar.jpg'
            ],
            [
                'firstName' => 'Marie',
                'lastName' => 'Dubois',
                'email' => 'marie.dubois@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'countryId' => 1, // France
                'city' => 'Paris',
                'profilePicture' => '/uploads/pfp/provider-2-avatar.webp'
            ],
            [
                'firstName' => 'Carlos',
                'lastName' => 'Garcia',
                'email' => 'carlos.garcia@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'countryId' => 10, // Mexique
                'city' => 'Mexico',
                'profilePicture' => '/uploads/pfp/provider-3-avatar.jpg'
            ],
            [
                'firstName' => 'Akira',
                'lastName' => 'Tanaka',
                'email' => 'akira.tanaka@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'countryId' => 3, // Japon
                'city' => 'Tokyo',
                'profilePicture' => '/uploads/pfp/provider-4-avatar.jpg'
            ],
            [
                'firstName' => 'Elena',
                'lastName' => 'Rossi',
                'email' => 'elena.rossi@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'countryId' => 4, // Italie
                'city' => 'Rome',
                'profilePicture' => '/uploads/pfp/provider-5-avatar.webp'
            ],
            [
                'firstName' => 'Raj',
                'lastName' => 'Patel',
                'email' => 'raj.patel@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'countryId' => 5, // Inde
                'city' => 'Mumbai',
                'profilePicture' => '/uploads/pfp/provider-6-avatar.webp'
            ]
        ];

        foreach ($providersData as $providerData) {
            $provider = new Provider(
                $providerData['firstName'],
                $providerData['lastName'],
                $providerData['email'],
                $providerData['password'],
                $providerData['countryId'],
                $providerData['city'],
                $providerData['profilePicture']
            );

            // Générer le slug automatiquement
            $slug = $this->slugificationService->generateProviderSlug(
                $providerData['firstName'],
                $providerData['lastName'],
                function ($slug) {
                    return $this->providerRepository->findBySlug($slug) !== null;
                }
            );
            $provider->setSlug($slug);

            $this->providerRepository->save($provider);
            echo "Provider créé : {$providerData['firstName']} {$providerData['lastName']} (slug: $slug)\n";
        }

        echo "✅ Fixtures Provider chargées avec succès.\n";
    }
}
