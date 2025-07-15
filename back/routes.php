<?php
require_once __DIR__ . '/vendor/autoload.php';

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;
use Soosuuke\IaPlatform\Config\Database;

if (defined('ROUTES_INCLUDED')) {
    return;
}
define('ROUTES_INCLUDED', true);

// Charge les variables d'environnement
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

session_start();
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-CSRF-Token');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Obtient l'instance PDO
$pdo = Database::connect();

// Dependency injection for controllers
$controllers = [
    'booking' => new Soosuuke\IaPlatform\Controller\BookingController(),
    'client' => new Soosuuke\IaPlatform\Controller\ClientController(
        new Soosuuke\IaPlatform\Repository\ClientRepository(),
        new Soosuuke\IaPlatform\Repository\BookingRepository(),
        new Soosuuke\IaPlatform\Repository\RequestRepository(),
        new Soosuuke\IaPlatform\Repository\ReviewRepository()
    ),
    'clientDashboard' => new Soosuuke\IaPlatform\Controller\ClientDashboardController(
        new Soosuuke\IaPlatform\Repository\ClientRepository(),
        new Soosuuke\IaPlatform\Repository\BookingRepository(),
        new Soosuuke\IaPlatform\Repository\RequestRepository(),
        new Soosuuke\IaPlatform\Repository\ReviewRepository()
    ),
    'completedWork' => new Soosuuke\IaPlatform\Controller\CompletedWorkController(),
    'completedWorkMedia' => new Soosuuke\IaPlatform\Controller\CompletedWorkMediaController(),
    'availabilitySlot' => new Soosuuke\IaPlatform\Controller\AvailabilitySlotController(
        new Soosuuke\IaPlatform\Repository\AvailabilitySlotRepository(),
        new Soosuuke\IaPlatform\Repository\ProviderRepository()
    ),
    'provider' => new Soosuuke\IaPlatform\Controller\ProviderController(
        new Soosuuke\IaPlatform\Repository\ProviderRepository(),
        new Soosuuke\IaPlatform\Repository\ProviderSkillRepository(),
        new Soosuuke\IaPlatform\Repository\AvailabilitySlotRepository()
    ),
    'providerDashboard' => new Soosuuke\IaPlatform\Controller\ProviderDashboardController(
        new Soosuuke\IaPlatform\Repository\ProviderRepository(),
        new Soosuuke\IaPlatform\Repository\ProviderSkillRepository(),
        new Soosuuke\IaPlatform\Repository\AvailabilitySlotRepository(),
        new Soosuuke\IaPlatform\Repository\CompletedWorkRepository(),
        new Soosuuke\IaPlatform\Repository\ReviewRepository(),
        new Soosuuke\IaPlatform\Repository\RequestRepository(),
        new Soosuuke\IaPlatform\Repository\ProviderDiplomaRepository(),
        new Soosuuke\IaPlatform\Repository\ProvidedServiceRepository(),
        $pdo
    ),
    'providerDiploma' => new Soosuuke\IaPlatform\Controller\ProviderDiplomaController(),
    'providedService' => new Soosuuke\IaPlatform\Controller\ProvidedServiceController(),
    'review' => new Soosuuke\IaPlatform\Controller\ReviewController(),
    'providerSkill' => new Soosuuke\IaPlatform\Controller\ProviderSkillController(
        new Soosuuke\IaPlatform\Repository\ProviderSkillRepository(),
        new Soosuuke\IaPlatform\Repository\SkillRepository()
    ),
    'request' => new Soosuuke\IaPlatform\Controller\RequestController(),
];

$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    $r->addGroup('/api/v1', function (RouteCollector $r) {
        // Client routes
        $r->addRoute('POST', '/clients/register', ['client', 'register']);
        $r->addRoute('POST', '/clients/login', ['client', 'login']);
        $r->addRoute('POST', '/clients/logout', ['client', 'logout']);
        $r->addRoute('GET', '/clients/{id:\d+}/dashboard', ['clientDashboard', 'dashboard']);
        $r->addRoute('PUT', '/clients/{id:\d+}/dashboard', ['clientDashboard', 'dashboard']);
        $r->addRoute('PATCH', '/clients/{id:\d+}/dashboard', ['clientDashboard', 'dashboard']);
        $r->addRoute('DELETE', '/clients/{id:\d+}/dashboard', ['clientDashboard', 'dashboard']);

        // Provider routes
        $r->addRoute('POST', '/providers/register', ['provider', 'register']);
        $r->addRoute('POST', '/providers/login', ['provider', 'login']);
        $r->addRoute('POST', '/providers/logout', ['provider', 'logout']);
        $r->addRoute('GET', '/providers/{id:\d+}/dashboard', ['providerDashboard', 'dashboard']);
        $r->addRoute('PUT', '/providers/{id:\d+}/dashboard', ['providerDashboard', 'dashboard']);
        $r->addRoute('PATCH', '/providers/{id:\d+}/dashboard', ['providerDashboard', 'dashboard']);
        $r->addRoute('DELETE', '/providers/{id:\d+}/dashboard', ['providerDashboard', 'dashboard']);
        $r->addRoute('GET', '/providers/{id:\d+}/profile', ['providerDashboard', 'publicProfile']);

        // Booking routes
        $r->addRoute('GET', '/bookings/{id:\d+}', ['booking', 'show']);
        $r->addRoute('POST', '/bookings', ['booking', 'create']);
        $r->addRoute('PUT', '/bookings/{id:\d+}', ['booking', 'update']);
        $r->addRoute('PATCH', '/bookings/{id:\d+}', ['booking', 'patch']);
        $r->addRoute('DELETE', '/bookings/{id:\d+}', ['booking', 'destroy']);

        // Availability slot routes
        $r->addRoute('GET', '/providers/{providerId:\d+}/slots', ['availabilitySlot', 'index']);
        $r->addRoute('POST', '/providers/{providerId:\d+}/slots', ['availabilitySlot', 'store']);
        $r->addRoute('PUT', '/slots/{id:\d+}', ['availabilitySlot', 'update']);
        $r->addRoute('PATCH', '/slots/{id:\d+}', ['availabilitySlot', 'patch']);
        $r->addRoute('DELETE', '/slots/{id:\d+}', ['availabilitySlot', 'destroy']);

        // Completed work routes
        $r->addRoute('GET', '/completed-works', ['completedWork', 'index']);
        $r->addRoute('GET', '/completed-works/{id:\d+}', ['completedWork', 'show']);
        $r->addRoute('POST', '/completed-works', ['completedWork', 'store']);
        $r->addRoute('PATCH', '/completed-works/{id:\d+}', ['completedWork', 'patch']);
        $r->addRoute('DELETE', '/completed-works/{id:\d+}', ['completedWork', 'destroy']);

        // Completed work media routes
        $r->addRoute('GET', '/completed-works/{workId:\d+}/media', ['completedWorkMedia', 'indexByWorkId']);
        $r->addRoute('POST', '/completed-work-media', ['completedWorkMedia', 'store']);
        $r->addRoute('PATCH', '/completed-work-media/{id:\d+}', ['completedWorkMedia', 'patch']);
        $r->addRoute('DELETE', '/completed-work-media/{id:\d+}', ['completedWorkMedia', 'destroy']);

        // Provided service routes
        $r->addRoute('GET', '/provided-services/{id:\d+}', ['providedService', 'show']);
        $r->addRoute('GET', '/providers/{providerId:\d+}/services', ['providedService', 'listByProvider']);
        $r->addRoute('POST', '/providers/{providerId:\d+}/services', ['providedService', 'create']);
        $r->addRoute('PUT', '/provided-services/{id:\d+}', ['providedService', 'update']);
        $r->addRoute('PATCH', '/provided-services/{id:\d+}', ['providedService', 'partialUpdate']);
        $r->addRoute('DELETE', '/provided-services/{id:\d+}', ['providedService', 'delete']);

        // Provider diploma routes
        $r->addRoute('GET', '/provider-diplomas', ['providerDiploma', 'index']);
        $r->addRoute('GET', '/provider-diplomas/{id:\d+}', ['providerDiploma', 'show']);
        $r->addRoute('POST', '/provider-diplomas', ['providerDiploma', 'store']);
        $r->addRoute('PATCH', '/provider-diplomas/{id:\d+}', ['providerDiploma', 'patch']);
        $r->addRoute('DELETE', '/provider-diplomas/{id:\d+}', ['providerDiploma', 'destroy']);

        // Review routes
        $r->addRoute('GET', '/providers/{providerId:\d+}/reviews', ['review', 'indexByProvider']);
        $r->addRoute('GET', '/reviews/{id:\d+}', ['review', 'show']);
        $r->addRoute('POST', '/reviews', ['review', 'store']);
        $r->addRoute('PATCH', '/reviews/{id:\d+}', ['review', 'patch']);
        $r->addRoute('DELETE', '/reviews/{id:\d+}', ['review', 'destroy']);

        // Provider skill routes
        $r->addRoute('GET', '/providers/{providerId:\d+}/skills', ['providerSkill', 'list']);
        $r->addRoute('POST', '/providers/{providerId:\d+}/skills', ['providerSkill', 'assign']);
        $r->addRoute('DELETE', '/providers/{providerId:\d+}/skills/{skillId:\d+}', ['providerSkill', 'remove']);

        // Request routes
        $r->addRoute('POST', '/requests', ['request', 'store']);
        $r->addRoute('GET', '/requests/provider/{providerId:\d+}', ['request', 'getByProvider']);
        $r->addRoute('GET', '/requests/client/{clientId:\d+}', ['request', 'getByClient']);
        $r->addRoute('PATCH', '/requests/{id:\d+}', ['request', 'updateStatus']);
        $r->addRoute('DELETE', '/requests/{id:\d+}', ['request', 'destroy']);

        // Root route
        $r->addRoute('GET', '/', [null, fn() => json_encode(['message' => 'Welcome to the IaPlatform API'])]);
    });
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo json_encode(['error' => 'Route not found']);
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        if ($handler[0] === null) {
            http_response_code(200);
            echo $handler[1]();
            break;
        }
        $controller = $controllers[$handler[0]];
        $action = $handler[1];
        $controller->$action(...array_values($vars));
        break;
}
session_write_close(); // Release session lock