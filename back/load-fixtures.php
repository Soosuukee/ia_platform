<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Soosuuke\IaPlatform\Config\Database;
use Soosuuke\IaPlatform\Fixtures\CountryFixtures;
use Soosuuke\IaPlatform\Fixtures\SkillFixtures;
use Soosuuke\IaPlatform\Fixtures\ClientFixtures;
use Soosuuke\IaPlatform\Fixtures\ProviderFixtures;

// 1. Chargement des variables d’environnement
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// 2. Connexion à la base de données
$pdo = Database::connect();

echo "🚀 Chargement des fixtures...\n\n";

// Charger dans l'ordre des dépendances
(new CountryFixtures())->load();
echo "\n";

(new SkillFixtures())->load();
echo "\n";

(new ClientFixtures())->load();
echo "\n";

(new ProviderFixtures())->load();
echo "\n";

echo "🎉 Toutes les fixtures ont été chargées avec succès.\n";
