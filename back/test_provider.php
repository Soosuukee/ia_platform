<?php
require_once 'vendor/autoload.php';

use Soosuuke\IaPlatform\Repository\ProviderRepository;

echo "Testing provider...\n";

try {
    $providerRepo = new ProviderRepository();
    $provider = $providerRepo->findByEmail('kevin.pro@example.com');

    if ($provider) {
        echo "✅ Provider Kevin found with ID: " . $provider->getId() . "\n";
        echo "✅ Email: " . $provider->getEmail() . "\n";
        echo "✅ Password hash: " . substr($provider->getPassword(), 0, 20) . "...\n";
        echo "✅ Password verification: " . (password_verify('pass456', $provider->getPassword()) ? 'OK' : 'FAILED') . "\n";
    } else {
        echo "❌ Provider Kevin not found!\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
