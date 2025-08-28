<?php
require_once 'vendor/autoload.php';

use Soosuuke\IaPlatform\Fixtures\ReviewFixtures;

echo "Loading review fixtures...\n";

try {
    $fixtures = new ReviewFixtures();
    $fixtures->load();
    echo "✅ Review fixtures loaded successfully!\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
