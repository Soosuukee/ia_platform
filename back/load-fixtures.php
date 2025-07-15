<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Soosuuke\IaPlatform\Config\Database;

use Soosuuke\IaPlatform\Fixtures\SkillFixtures;
use Soosuuke\IaPlatform\Fixtures\ClientFixtures;
use Soosuuke\IaPlatform\Fixtures\ProviderFixtures;

// 1. Chargement des variables d’environnement
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// 2. Connexion à la base de données
$pdo = Database::connect();

// 3. Chargement des fixtures
echo "Chargement des fixtures...\n";

SkillFixtures::load();               // Compétences
(new ClientFixtures())->load();     // Clients
(new ProviderFixtures())->load();   // Providers + tout le reste

echo "🎉 Toutes les fixtures ont été chargées avec succès.\n";
