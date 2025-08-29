<?php

require_once __DIR__ . '/vendor/autoload.php';

use PDO;
use PDOException;

echo "🚀 Initialisation de la base de données...\n";

try {
    // Connexion à MySQL sans spécifier de base de données
    $pdo = new PDO('mysql:host=localhost;port=3307;charset=utf8mb4', 'root', 'root', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Créer la base de données si elle n'existe pas
    $pdo->exec('CREATE DATABASE IF NOT EXISTS dev_db');
    echo "✅ Base de données 'dev_db' créée ou existante.\n";

    // Se connecter à la base de données
    $pdo->exec('USE dev_db');

    // Lire et exécuter le script SQL
    $sql = file_get_contents(__DIR__ . '/src/sql/ia_platform_schema.sql');

    // Supprimer les lignes de commentaires et les lignes vides
    $sql = preg_replace('/--.*$/m', '', $sql);
    $sql = preg_replace('/^\s*$/m', '', $sql);

    // Exécuter les requêtes
    $statements = explode(';', $sql);
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }

    echo "✅ Tables créées avec succès.\n";
    echo "✅ Base de données initialisée.\n";
} catch (PDOException $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    exit(1);
}
