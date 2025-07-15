<?php
require __DIR__ . '/../../vendor/autoload.php';

use Soosuuke\IaPlatform\Config\Database;

try {
    $db = Database::connect();
    echo "Connexion à la base de données réussie !";
} catch (Exception $e) {
    echo "Échec de la connexion : " . $e->getMessage();
}
