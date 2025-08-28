<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Fixtures;

use Soosuuke\IaPlatform\Config\Database;
use Soosuuke\IaPlatform\Entity\Client;
use Soosuuke\IaPlatform\Repository\ClientRepository;
use Soosuuke\IaPlatform\Service\ClientSlugificationService;

class ClientFixtures
{
    private \PDO $pdo;
    private ClientRepository $clientRepository;
    private ClientSlugificationService $slugificationService;

    public function __construct()
    {
        $this->pdo = Database::connect();
        $this->clientRepository = new ClientRepository();
        $this->slugificationService = new ClientSlugificationService();
    }

    public function load(): void
    {
        echo "Chargement des fixtures Client...\n";

        // Données des clients depuis le JSON
        $clientsData = [
            [
                'firstName' => 'Alexandre',
                'lastName' => 'Martin',
                'email' => 'alexandre.martin@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'countryId' => 1, // France
                'city' => 'Paris',
                'profilePicture' => '/uploads/pfp/client-1-avatar.webp'
            ],
            [
                'firstName' => 'Sarah',
                'lastName' => 'Johnson',
                'email' => 'sarah.johnson@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'countryId' => 2, // États-Unis
                'city' => 'New York',
                'profilePicture' => '/uploads/pfp/client-2-avatar.avif'
            ],
            [
                'firstName' => 'Yuki',
                'lastName' => 'Tanaka',
                'email' => 'yuki.tanaka@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'countryId' => 3, // Japon
                'city' => 'Tokyo',
                'profilePicture' => '/uploads/pfp/client-3-avatar.webp'
            ],
            [
                'firstName' => 'Maria',
                'lastName' => 'Garcia',
                'email' => 'maria.garcia@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'countryId' => 6, // Espagne
                'city' => 'Madrid',
                'profilePicture' => '/uploads/pfp/client-4-avatar.jpg'
            ],
            [
                'firstName' => 'David',
                'lastName' => 'Chen',
                'email' => 'david.chen@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'countryId' => 7, // Singapour
                'city' => 'Singapore',
                'profilePicture' => '/uploads/pfp/client-5-avatar.webp'
            ],
            [
                'firstName' => 'Emma',
                'lastName' => 'Wilson',
                'email' => 'emma.wilson@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'countryId' => 8, // Royaume-Uni
                'city' => 'London',
                'profilePicture' => '/uploads/pfp/client-6-avatar.jpg'
            ]
        ];

        foreach ($clientsData as $clientData) {
            $client = new Client(
                $clientData['firstName'],
                $clientData['lastName'],
                $clientData['email'],
                $clientData['password'],
                $clientData['countryId'],
                $clientData['city'],
                $clientData['profilePicture']
            );

            // Générer le slug automatiquement
            $slug = $this->slugificationService->generateClientSlug(
                $clientData['firstName'],
                $clientData['lastName'],
                function ($slug) {
                    return $this->clientRepository->findBySlug($slug) !== null;
                }
            );
            $client->setSlug($slug);

            $this->clientRepository->save($client);
            echo "Client créé : {$clientData['firstName']} {$clientData['lastName']} (slug: $slug)\n";
        }

        echo "✅ Fixtures Client chargées avec succès.\n";
    }
}
