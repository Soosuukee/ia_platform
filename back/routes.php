<?php
// Headers CORS - DOIT être en premier avant tout autre output
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, Accept, X-Requested-With, Origin');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400');

// Gestion des requêtes OPTIONS (preflight) - DOIT être traité immédiatement
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/vendor/autoload.php';

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;
use Soosuuke\IaPlatform\Config\AuthMiddleware;

// 🖼️ Servir les fichiers statiques directement (avant FastRoute)
$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);

if (preg_match('#^/api/v1/images/(.+)$#', $path, $matches)) {
    $relativePath = $matches[1];
    $imagePath = __DIR__ . '/images/' . $relativePath;

    error_log("Image request: $path -> $imagePath");

    if (file_exists($imagePath) && is_file($imagePath)) {
        $mimeType = mime_content_type($imagePath);
        error_log("Image found: $mimeType");
        if (str_starts_with($mimeType, 'image/')) {
            header('Content-Type: ' . $mimeType);
            header('Content-Length: ' . filesize($imagePath));
            header('Cache-Control: public, max-age=31536000');
            readfile($imagePath);
            exit;
        }
    }

    // Image non trouvée
    error_log("Image not found: $imagePath");
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Image not found', 'path' => $imagePath, 'exists' => file_exists($imagePath)]);
    exit;
}

// Dependency injection for controllers
$controllers = [
    'auth' => new Soosuuke\IaPlatform\Controller\AuthController(),
    'provider' => new Soosuuke\IaPlatform\Controller\ProviderController(),
    'client' => new Soosuuke\IaPlatform\Controller\ClientController(),
    'service' => new Soosuuke\IaPlatform\Controller\ServiceController(),
    'article' => new Soosuuke\IaPlatform\Controller\ArticleController(),
    'skill' => new Soosuuke\IaPlatform\Controller\SkillController(),
    'job' => new Soosuuke\IaPlatform\Controller\JobController(),
    'language' => new Soosuuke\IaPlatform\Controller\LanguageController(),
    'country' => new Soosuuke\IaPlatform\Controller\CountryController(),
];

$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    $r->addGroup('/api/v1', function (RouteCollector $r) {
        // Auth routes
        $r->addRoute('POST', '/auth/login', ['auth', 'login']);
        $r->addRoute('POST', '/auth/logout', ['auth', 'logout']);
        $r->addRoute('GET', '/auth/me', ['auth', 'getCurrentUser']);
        $r->addRoute('POST', '/auth/register', ['auth', 'register']);
        $r->addRoute('POST', '/auth/change-password', ['auth', 'changePassword']);

        // Provider routes
        $r->addRoute('GET', '/providers', ['provider', 'getAllProviders']);
        $r->addRoute('GET', '/providers/{id:\d+}', ['provider', 'getProviderById']);
        $r->addRoute('GET', '/providers/email/{email}', ['provider', 'getProviderByEmail']);
        $r->addRoute('GET', '/providers/slug/{slug}', ['provider', 'getProviderBySlug']);
        $r->addRoute('POST', '/providers', ['provider', 'createProvider']);
        $r->addRoute('PUT', '/providers/{id:\d+}', ['provider', 'updateProvider']);
        $r->addRoute('DELETE', '/providers/{id:\d+}', ['provider', 'deleteProvider']);
        $r->addRoute('POST', '/providers/{id:\d+}/profile-picture', ['provider', 'uploadProfilePicture']);

        // Client routes
        $r->addRoute('POST', '/clients/{id:\d+}/profile-picture', ['client', 'uploadProfilePicture']);
        $r->addRoute('GET', '/clients', ['client', 'getAllClients']);
        $r->addRoute('GET', '/clients/{id:\d+}', ['client', 'getClientById']);
        $r->addRoute('GET', '/clients/email/{email}', ['client', 'getClientByEmail']);
        $r->addRoute('GET', '/clients/slug/{slug}', ['client', 'getClientBySlug']);
        $r->addRoute('POST', '/clients', ['client', 'createClient']);
        $r->addRoute('PUT', '/clients/{id:\d+}', ['client', 'updateClient']);
        $r->addRoute('DELETE', '/clients/{id:\d+}', ['client', 'deleteClient']);

        // Service routes
        $r->addRoute('GET', '/services', ['service', 'getAllServices']);
        $r->addRoute('GET', '/services/{id:\d+}', ['service', 'getServiceById']);
        $r->addRoute('GET', '/services/slug/{slug}', ['service', 'getServiceBySlug']);
        $r->addRoute('GET', '/services/provider/{providerId:\d+}', ['service', 'getServicesByProviderId']);
        $r->addRoute('GET', '/services/active', ['service', 'getActiveServices']);
        $r->addRoute('GET', '/services/featured', ['service', 'getFeaturedServices']);
        $r->addRoute('POST', '/services', ['service', 'createService']);
        $r->addRoute('PUT', '/services/{id:\d+}', ['service', 'updateService']);
        $r->addRoute('DELETE', '/services/{id:\d+}', ['service', 'deleteService']);
        $r->addRoute('GET', '/services/{id:\d+}/with-content', ['service', 'getServiceWithContent']);
        $r->addRoute('POST', '/services/with-content', ['service', 'createServiceWithContent']);
        $r->addRoute('POST', '/services/{id:\d+}/cover', ['service', 'uploadCover']);
        $r->addRoute('POST', '/services/{serviceId:\d+}/content/{contentId:\d+}/images', ['service', 'uploadImage']);

        // Article routes
        $r->addRoute('GET', '/articles', ['article', 'getAllArticles']);
        $r->addRoute('GET', '/articles/{id:\d+}', ['article', 'getArticleById']);
        $r->addRoute('GET', '/articles/slug/{slug}', ['article', 'getArticleBySlug']);
        $r->addRoute('GET', '/articles/provider/{providerId:\d+}', ['article', 'getArticlesByProviderId']);
        $r->addRoute('GET', '/articles/published', ['article', 'getPublishedArticles']);
        $r->addRoute('GET', '/articles/featured', ['article', 'getFeaturedArticles']);
        $r->addRoute('POST', '/articles', ['article', 'createArticle']);
        $r->addRoute('PUT', '/articles/{id:\d+}', ['article', 'updateArticle']);
        $r->addRoute('DELETE', '/articles/{id:\d+}', ['article', 'deleteArticle']);
        $r->addRoute('GET', '/articles/{id:\d+}/with-content', ['article', 'getArticleWithContent']);
        $r->addRoute('POST', '/articles/with-content', ['article', 'createArticleWithContent']);
        $r->addRoute('POST', '/articles/{id:\d+}/cover', ['article', 'uploadCover']);
        $r->addRoute('POST', '/articles/{articleId:\d+}/content/{contentId:\d+}/images', ['article', 'uploadImage']);

        // Skill routes
        $r->addRoute('GET', '/soft-skills', ['skill', 'getAllSoftSkills']);
        $r->addRoute('GET', '/soft-skills/{id:\d+}', ['skill', 'getSoftSkillById']);
        $r->addRoute('POST', '/soft-skills', ['skill', 'createSoftSkill']);
        $r->addRoute('PUT', '/soft-skills/{id:\d+}', ['skill', 'updateSoftSkill']);
        $r->addRoute('DELETE', '/soft-skills/{id:\d+}', ['skill', 'deleteSoftSkill']);

        $r->addRoute('GET', '/hard-skills', ['skill', 'getAllHardSkills']);
        $r->addRoute('GET', '/hard-skills/{id:\d+}', ['skill', 'getHardSkillById']);
        $r->addRoute('POST', '/hard-skills', ['skill', 'createHardSkill']);
        $r->addRoute('PUT', '/hard-skills/{id:\d+}', ['skill', 'updateHardSkill']);
        $r->addRoute('DELETE', '/hard-skills/{id:\d+}', ['skill', 'deleteHardSkill']);

        // Job routes
        $r->addRoute('GET', '/jobs', ['job', 'getAllJobs']);
        $r->addRoute('GET', '/jobs/{id:\d+}', ['job', 'getJobById']);
        $r->addRoute('POST', '/jobs', ['job', 'createJob']);
        $r->addRoute('PUT', '/jobs/{id:\d+}', ['job', 'updateJob']);
        $r->addRoute('DELETE', '/jobs/{id:\d+}', ['job', 'deleteJob']);

        // Language routes
        $r->addRoute('GET', '/languages', ['language', 'getAllLanguages']);
        $r->addRoute('GET', '/languages/{id:\d+}', ['language', 'getLanguageById']);
        $r->addRoute('POST', '/languages', ['language', 'createLanguage']);
        $r->addRoute('PUT', '/languages/{id:\d+}', ['language', 'updateLanguage']);
        $r->addRoute('DELETE', '/languages/{id:\d+}', ['language', 'deleteLanguage']);

        // Country routes
        $r->addRoute('GET', '/countries', ['country', 'getAllCountries']);
        $r->addRoute('GET', '/countries/{id:\d+}', ['country', 'getCountryById']);
        $r->addRoute('POST', '/countries', ['country', 'createCountry']);
        $r->addRoute('PUT', '/countries/{id:\d+}', ['country', 'updateCountry']);
        $r->addRoute('DELETE', '/countries/{id:\d+}', ['country', 'deleteCountry']);

        // Provider skills routes
        $r->addRoute('GET', '/providers/{providerId:\d+}/soft-skills', ['provider', 'getProviderSoftSkills']);
        $r->addRoute('POST', '/providers/{providerId:\d+}/soft-skills/{skillId:\d+}', ['provider', 'addSoftSkillToProvider']);
        $r->addRoute('DELETE', '/providers/{providerId:\d+}/soft-skills/{skillId:\d+}', ['provider', 'removeSoftSkillFromProvider']);

        $r->addRoute('GET', '/providers/{providerId:\d+}/hard-skills', ['provider', 'getProviderHardSkills']);
        $r->addRoute('POST', '/providers/{providerId:\d+}/hard-skills/{skillId:\d+}', ['provider', 'addHardSkillToProvider']);
        $r->addRoute('DELETE', '/providers/{providerId:\d+}/hard-skills/{skillId:\d+}', ['provider', 'removeHardSkillFromProvider']);

        // Provider jobs routes
        $r->addRoute('GET', '/providers/{providerId:\d+}/jobs', ['provider', 'getProviderJobs']);
        $r->addRoute('POST', '/providers/{providerId:\d+}/jobs/{jobId:\d+}', ['provider', 'addJobToProvider']);
        $r->addRoute('DELETE', '/providers/{providerId:\d+}/jobs/{jobId:\d+}', ['provider', 'removeJobFromProvider']);

        // Provider languages routes
        $r->addRoute('GET', '/providers/{providerId:\d+}/languages', ['provider', 'getProviderLanguages']);
        $r->addRoute('POST', '/providers/{providerId:\d+}/languages/{languageId:\d+}', ['provider', 'addLanguageToProvider']);
        $r->addRoute('DELETE', '/providers/{providerId:\d+}/languages/{languageId:\d+}', ['provider', 'removeLanguageFromProvider']);

        // Provider education routes
        $r->addRoute('GET', '/providers/{providerId:\d+}/education', ['provider', 'getProviderEducation']);
        $r->addRoute('POST', '/providers/{providerId:\d+}/education', ['provider', 'addEducationToProvider']);
        $r->addRoute('PUT', '/providers/{providerId:\d+}/education/{educationId:\d+}', ['provider', 'updateProviderEducation']);
        $r->addRoute('DELETE', '/providers/{providerId:\d+}/education/{educationId:\d+}', ['provider', 'removeEducationFromProvider']);

        // Provider experience routes
        $r->addRoute('GET', '/providers/{providerId:\d+}/experience', ['provider', 'getProviderExperience']);
        $r->addRoute('POST', '/providers/{providerId:\d+}/experience', ['provider', 'addExperienceToProvider']);
        $r->addRoute('PUT', '/providers/{providerId:\d+}/experience/{experienceId:\d+}', ['provider', 'updateProviderExperience']);
        $r->addRoute('DELETE', '/providers/{providerId:\d+}/experience/{experienceId:\d+}', ['provider', 'removeExperienceFromProvider']);

        // Upload routes pour les médias
        $r->addRoute('POST', '/providers/{providerId:\d+}/experience/{experienceId:\d+}/logo', ['provider', 'uploadExperienceLogo']);
        $r->addRoute('POST', '/providers/{providerId:\d+}/education/{educationId:\d+}/logo', ['provider', 'uploadEducationLogo']);
        $r->addRoute('POST', '/providers/{providerId:\d+}/completed-works/{workId:\d+}/media', ['provider', 'uploadCompletedWorkMedia']);

        // Routes avec slugs pour les URLs SEO-friendly
        $r->addRoute('GET', '/providers/{slug}', ['provider', 'getProviderBySlug']);
        $r->addRoute('GET', '/clients/{slug}', ['client', 'getClientBySlug']);
        $r->addRoute('GET', '/services/{slug}', ['service', 'getServiceBySlug']);
        $r->addRoute('GET', '/articles/{slug}', ['article', 'getArticleBySlug']);

        // Routes imbriquées pour les services et articles d'un provider
        $r->addRoute('GET', '/providers/{providerSlug}/services/{serviceSlug}', ['service', 'getServiceByProviderAndSlug']);
        $r->addRoute('GET', '/providers/{providerSlug}/articles/{articleSlug}', ['article', 'getArticleByProviderAndSlug']);
    });
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

// Content-Type pour les requêtes non-OPTIONS (sauf les images)
if ($_SERVER['REQUEST_METHOD'] !== 'OPTIONS' && !str_contains($uri, '/images/')) {
    header('Content-Type: application/json');
}

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

        if (is_callable($handler)) {
            $handler(...array_values($vars));
            break;
        }

        if ($handler[0] === null) {
            http_response_code(200);
            $handler[1]();
            break;
        }

        $controller = $controllers[$handler[0]];
        $action = $handler[1];

        // Vérifier si la route nécessite une authentification
        $routePath = $uri;
        $httpMethod = $_SERVER['REQUEST_METHOD'];

        // Routes publiques (pas d'authentification requise)
        $publicRoutes = [
            'POST' => ['/api/v1/auth/login', '/api/v1/auth/register'],
            'GET' => [
                '/api/v1/providers',
                '/api/v1/providers/{id:\d+}',
                '/api/v1/providers/slug/{slug}',
                '/api/v1/services',
                '/api/v1/services/{id:\d+}',
                '/api/v1/services/slug/{slug}',
                '/api/v1/services/active',
                '/api/v1/services/featured',
                '/api/v1/articles',
                '/api/v1/articles/{id:\d+}',
                '/api/v1/articles/slug/{slug}',
                '/api/v1/articles/published',
                '/api/v1/articles/featured',
                '/api/v1/skills',
                '/api/v1/jobs',
                '/api/v1/languages',
                '/api/v1/countries',
                '/api/v1/providers/{slug}',
                '/api/v1/services/{slug}',
                '/api/v1/articles/{slug}'
            ]
        ];

        // Vérifier si la route actuelle est publique
        $isPublicRoute = false;
        if (isset($publicRoutes[$httpMethod])) {
            foreach ($publicRoutes[$httpMethod] as $publicRoute) {
                $pattern = '#^' . str_replace(['{id:\d+}', '{slug}'], ['\d+', '[^/]+'], $publicRoute) . '$#';
                if (preg_match($pattern, $routePath)) {
                    $isPublicRoute = true;
                    break;
                }
            }
        }

        // Si la route n'est pas publique, vérifier l'authentification
        if (!$isPublicRoute && !AuthMiddleware::isAuthenticated()) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Authentification requise'
            ]);
            break;
        }

        // Récupérer les données JSON pour les requêtes POST/PUT
        $data = [];
        if (in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT', 'PATCH'])) {
            $input = file_get_contents('php://input');
            if ($input) {
                $data = json_decode($input, true) ?? [];
            }
        }

        // Appeler la méthode du controller avec les paramètres
        if (!empty($data)) {
            $args = array_values($vars);
            $args[] = $data;
            $controller->$action(...$args);
        } else {
            $controller->$action(...array_values($vars));
        }
        break;
}
