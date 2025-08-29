<?php
require_once 'vendor/autoload.php';

use Dotenv\Dotenv;

try {
    $dotenv = Dotenv::createImmutable('.');
    $dotenv->load();

    echo "DB_HOST: " . $_ENV['DB_HOST'] . "\n";
    echo "DB_PORT: " . $_ENV['DB_PORT'] . "\n";
    echo "DB_NAME: " . $_ENV['DB_NAME'] . "\n";
    echo "DB_USER: " . $_ENV['DB_USER'] . "\n";
    echo "DB_PASS: " . $_ENV['DB_PASS'] . "\n";
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
