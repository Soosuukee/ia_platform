<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Fixtures;

use Soosuuke\IaPlatform\Config\Database;
use Soosuuke\IaPlatform\Entity\Provider;
use Soosuuke\IaPlatform\Repository\ProviderRepository;
use Soosuuke\IaPlatform\Service\ProviderSlugificationService;
use Soosuuke\IaPlatform\Service\ProviderImageService;

class ProviderFixtures
{
    private \PDO $pdo;
    private ProviderRepository $providerRepository;
    private ProviderSlugificationService $slugificationService;
    private ProviderImageService $imageService;

    public function __construct()
    {
        $this->pdo = Database::connect();
        $this->providerRepository = new ProviderRepository();
        $this->slugificationService = new ProviderSlugificationService();
        $this->imageService = new ProviderImageService();
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
                'jobId' => 7, // Expert en Optimisation GPU
                'countryId' => 9, // Taïwan
                'city' => 'Taipei',
                'profilePicture' => 'avatar-jh.jpg'
            ],
            [
                'firstName' => 'Marie',
                'lastName' => 'Dubois',
                'email' => 'marie.dubois@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'jobId' => 2, // Ingénieure Machine Learning
                'countryId' => 1, // France
                'city' => 'Paris',
                'profilePicture' => 'avatar-md.webp'
            ],
            [
                'firstName' => 'Carlos',
                'lastName' => 'Garcia',
                'email' => 'carlos.garcia@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'jobId' => 3, // Data Scientist
                'countryId' => 10, // Mexique
                'city' => 'Mexico',
                'profilePicture' => 'avatar-gc.jpg'
            ],
            [
                'firstName' => 'Akira',
                'lastName' => 'Tanaka',
                'email' => 'akira.tanaka@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'jobId' => 5, // Ingénieure Vision par Ordinateur
                'countryId' => 3, // Japon
                'city' => 'Tokyo',
                'profilePicture' => 'avatar-at.jpg'
            ],
            [
                'firstName' => 'Elena',
                'lastName' => 'Rossi',
                'email' => 'elena.rossi@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'jobId' => 10, // Spécialiste en Traitement du Langage Naturel
                'countryId' => 4, // Italie
                'city' => 'Rome',
                'profilePicture' => 'avatar-er.webp'
            ],
            [
                'firstName' => 'Raj',
                'lastName' => 'Patel',
                'email' => 'raj.patel@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'jobId' => 6, // Spécialiste Deep Learning
                'countryId' => 5, // Inde
                'city' => 'Mumbai',
                'profilePicture' => 'avatar-rp.webp'
            ]
        ];

        foreach ($providersData as $providerData) {
            $provider = new Provider(
                $providerData['firstName'],
                $providerData['lastName'],
                $providerData['email'],
                $providerData['password'],
                $providerData['jobId'],
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

            // Créer la structure de dossiers et copier les images
            $this->imageService->createProviderImageStructure($provider->getId(), $providerData['profilePicture']);

            echo "Provider créé : {$providerData['firstName']} {$providerData['lastName']} (slug: $slug)\n";
        }

        echo "✅ Fixtures Provider chargées avec succès.\n";
    }
}
