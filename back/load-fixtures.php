<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Soosuuke\IaPlatform\Config\Database;
use Soosuuke\IaPlatform\Fixtures\CountryFixtures;
use Soosuuke\IaPlatform\Fixtures\JobFixtures;
use Soosuuke\IaPlatform\Fixtures\LanguageFixtures;
use Soosuuke\IaPlatform\Fixtures\SkillFixtures;
use Soosuuke\IaPlatform\Fixtures\ClientFixtures;
use Soosuuke\IaPlatform\Fixtures\ProviderFixtures;
use Soosuuke\IaPlatform\Fixtures\ServiceFixtures;
use Soosuuke\IaPlatform\Fixtures\ArticleFixtures;
use Soosuuke\IaPlatform\Fixtures\TagFixtures;
use Soosuuke\IaPlatform\Fixtures\ExperienceFixtures;
use Soosuuke\IaPlatform\Fixtures\EducationFixtures;

// 1. Chargement des variables d'environnement
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// 2. Connexion à la base de données
$pdo = Database::connect();

echo "🚀 Chargement des fixtures...\n\n";

// Charger dans l'ordre des dépendances
(new CountryFixtures())->load();
echo "\n";

(new JobFixtures())->load();
echo "\n";

(new LanguageFixtures())->load();
echo "\n";

(new SkillFixtures())->load();
echo "\n";

(new ClientFixtures())->load();
echo "\n";

(new ProviderFixtures())->load();
echo "\n";

(new ServiceFixtures())->load();
echo "\n";

(new ArticleFixtures())->load();
echo "\n";

(new ExperienceFixtures())->load();
echo "\n";

(new EducationFixtures())->load();
echo "\n";

(new TagFixtures())->load();
echo "\n";

echo "🎉 Toutes les fixtures ont été chargées avec succès.\n";
