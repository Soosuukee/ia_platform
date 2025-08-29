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
    'softSkill' => new Soosuuke\IaPlatform\Controller\SoftSkillController(),
    'hardSkill' => new Soosuuke\IaPlatform\Controller\HardSkillController(),
    'completedWork' => new Soosuuke\IaPlatform\Controller\CompletedWorkController(),
    'job' => new Soosuuke\IaPlatform\Controller\JobController(),
    'language' => new Soosuuke\IaPlatform\Controller\LanguageController(),
    'country' => new Soosuuke\IaPlatform\Controller\CountryController(),
    'tag' => new Soosuuke\IaPlatform\Controller\TagController(),
    'providerImage' => new Soosuuke\IaPlatform\Controller\ProviderImageController(),
    'clientImage' => new Soosuuke\IaPlatform\Controller\ClientImageController(),
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
        $r->addRoute('GET', '/providers/slug/{slug}', ['provider', 'getProviderBySlug']);
        $r->addRoute('GET', '/providers/country/{countryId:\d+}', ['provider', 'getProvidersByCountry']);
        $r->addRoute('GET', '/providers/job/{jobId:\d+}', ['provider', 'getProvidersByJob']);
        $r->addRoute('GET', '/providers/hard-skill/{skillName}', ['provider', 'getProvidersByHardSkill']);
        $r->addRoute('GET', '/providers/soft-skill/{skillName}', ['provider', 'getProvidersBySoftSkill']);
        $r->addRoute('GET', '/providers/language/{languageName}', ['provider', 'getProvidersByLanguage']);
        $r->addRoute('GET', '/providers/search/{query}', ['provider', 'searchProviders']);
        $r->addRoute('GET', '/providers/{providerSlug}/reviews', ['provider', 'getProviderReviews']);
        $r->addRoute('GET', '/providers/{providerSlug}/availability', ['provider', 'getProviderAvailability']);
        $r->addRoute('POST', '/providers', ['provider', 'createProvider']);
        $r->addRoute('PUT', '/providers/{id:\d+}', ['provider', 'updateProvider']);
        $r->addRoute('PATCH', '/providers/{id:\d+}', ['provider', 'updateProvider']);
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
        $r->addRoute('PATCH', '/clients/{id:\d+}', ['client', 'updateClient']);
        $r->addRoute('DELETE', '/clients/{id:\d+}', ['client', 'deleteClient']);

        // Service routes
        $r->addRoute('GET', '/services', ['service', 'getAllServices']);
        // Accès par ID désactivé pour les services
        // Accès par slug direct désactivé pour les services
        $r->addRoute('GET', '/providers/slug/{providerSlug}/services', ['service', 'getServicesByProviderSlug']);
        $r->addRoute('GET', '/providers/slug/{providerSlug}/services/{serviceSlug}', ['service', 'getServiceByProviderAndSlug']);
        $r->addRoute('GET', '/providers/{providerSlug}/services', ['service', 'getServicesByProviderSlug']);
        $r->addRoute('GET', '/providers/{providerSlug}/services/{serviceSlug}', ['service', 'getServiceByProviderAndSlug']);
        $r->addRoute('GET', '/providers/{providerId:\d+}/services', ['service', 'getServicesByProviderId']);
        $r->addRoute('GET', '/services/{id:\d+}', ['service', 'getServiceById']);
        $r->addRoute('GET', '/providers/slug/{providerSlug}/experiences', ['provider', 'getProviderExperiences']);
        $r->addRoute('GET', '/providers/slug/{providerSlug}/educations', ['provider', 'getProviderEducations']);
        $r->addRoute('GET', '/services/active', ['service', 'getActiveServices']);
        $r->addRoute('GET', '/services/featured', ['service', 'getFeaturedServices']);
        $r->addRoute('GET', '/services/tag/{tagId:\d+}', ['service', 'getServicesByTag']);
        $r->addRoute('GET', '/services/tag/slug/{tagSlug}', ['service', 'getServicesByTagSlug']);
        $r->addRoute('GET', '/services/search/{query}', ['service', 'searchServices']);
        $r->addRoute('POST', '/services', ['service', 'createService']);
        $r->addRoute('PUT', '/services/{id:\d+}', ['service', 'updateService']);
        $r->addRoute('PATCH', '/services/{id:\d+}', ['service', 'updateService']);
        $r->addRoute('DELETE', '/services/{id:\d+}', ['service', 'deleteService']);
        // plus de with-content: renvoyé par défaut
        $r->addRoute('POST', '/services/with-content', ['service', 'createServiceWithContent']);
        $r->addRoute('PATCH', '/services/{id:\d+}/with-content', ['service', 'patchServiceWithContent']);
        $r->addRoute('POST', '/services/{id:\d+}/cover', ['service', 'uploadCover']);
        // Provider scoped uploads for service media
        $r->addRoute('POST', '/providers/{providerId:\d+}/services/{serviceId:\d+}/cover', ['providerImage', 'uploadServiceCover']);
        $r->addRoute('POST', '/providers/{providerId:\d+}/services/{serviceId:\d+}/sections/{sectionId:\d+}/contents/{contentId:\d+}/images', ['providerImage', 'uploadServiceContentImage']);
        $r->addRoute('POST', '/providers/{providerId:\d+}/{entityType:articles|experiences|education}/{entityId:\d+}/images', ['providerImage', 'uploadEntityImage']);
        $r->addRoute('POST', '/services/{serviceId:\d+}/content/{contentId:\d+}/images', ['service', 'uploadImage']);
        $r->addRoute('PUT', '/services/slug/{slug}', ['service', 'updateServiceBySlug']);
        $r->addRoute('PATCH', '/services/slug/{slug}', ['service', 'updateServiceBySlug']);
        $r->addRoute('DELETE', '/services/slug/{slug}', ['service', 'deleteServiceBySlug']);

        // Article routes
        $r->addRoute('GET', '/articles', ['article', 'getAllArticles']);
        // Accès par ID désactivé pour les articles
        // Accès par slug direct désactivé pour les articles
        $r->addRoute('GET', '/providers/slug/{providerSlug}/articles', ['article', 'getArticlesByProviderSlug']);
        $r->addRoute('GET', '/providers/slug/{providerSlug}/articles/{articleSlug}', ['article', 'getArticleByProviderAndSlug']);
        $r->addRoute('GET', '/providers/{providerSlug}/articles', ['article', 'getArticlesByProviderSlug']);
        $r->addRoute('GET', '/providers/{providerSlug}/articles/{articleSlug}', ['article', 'getArticleByProviderAndSlug']);
        $r->addRoute('GET', '/articles/published', ['article', 'getPublishedArticles']);
        $r->addRoute('GET', '/articles/featured', ['article', 'getFeaturedArticles']);
        $r->addRoute('GET', '/articles/tag/{tagId:\d+}', ['article', 'getArticlesByTag']);
        $r->addRoute('GET', '/articles/tag/slug/{tagSlug}', ['article', 'getArticlesByTagSlug']);
        $r->addRoute('GET', '/articles/search/{query}', ['article', 'searchArticles']);
        $r->addRoute('POST', '/articles', ['article', 'createArticle']);
        $r->addRoute('PUT', '/articles/{id:\d+}', ['article', 'updateArticle']);
        $r->addRoute('PATCH', '/articles/{id:\d+}', ['article', 'updateArticle']);
        $r->addRoute('PUT', '/articles/slug/{slug}', ['article', 'updateArticleBySlug']);
        $r->addRoute('PATCH', '/articles/slug/{slug}', ['article', 'updateArticleBySlug']);
        // Completed works routes
        $r->addRoute('GET', '/completed-works', ['completedWork', 'index']);
        $r->addRoute('GET', '/completed-works/{id:\d+}', ['completedWork', 'show']);
        $r->addRoute('POST', '/completed-works', ['completedWork', 'store']);
        $r->addRoute('PATCH', '/completed-works/{id:\d+}', ['completedWork', 'patch']);
        $r->addRoute('DELETE', '/completed-works/{id:\d+}', ['completedWork', 'destroy']);
        $r->addRoute('DELETE', '/articles/{id:\d+}', ['article', 'deleteArticle']);
        // plus de with-content: renvoyé par défaut
        $r->addRoute('POST', '/articles/with-content', ['article', 'createArticleWithContent']);
        $r->addRoute('PATCH', '/articles/{id:\d+}/with-content', ['article', 'patchArticleWithContent']);
        $r->addRoute('POST', '/articles/{id:\d+}/cover', ['article', 'uploadCover']);
        $r->addRoute('POST', '/articles/{articleId:\d+}/content/{contentId:\d+}/images', ['article', 'uploadImage']);
        $r->addRoute('DELETE', '/articles/{articleId:\d+}/content/{contentId:\d+}/images/{imageId:\d+}', ['article', 'deleteContentImage']);
        $r->addRoute('DELETE', '/articles/slug/{slug}', ['article', 'deleteArticleBySlug']);

        // Soft skill routes
        $r->addRoute('GET', '/soft-skills', ['softSkill', 'getAllSoftSkills']);
        $r->addRoute('GET', '/soft-skills/{id:\d+}', ['softSkill', 'getSoftSkillById']);
        $r->addRoute('POST', '/soft-skills', ['softSkill', 'createSoftSkill']);
        $r->addRoute('PUT', '/soft-skills/{id:\d+}', ['softSkill', 'updateSoftSkill']);
        $r->addRoute('DELETE', '/soft-skills/{id:\d+}', ['softSkill', 'deleteSoftSkill']);

        // Hard skill routes
        $r->addRoute('GET', '/hard-skills', ['hardSkill', 'getAllHardSkills']);
        $r->addRoute('GET', '/hard-skills/{id:\d+}', ['hardSkill', 'getHardSkillById']);
        $r->addRoute('POST', '/hard-skills', ['hardSkill', 'createHardSkill']);
        $r->addRoute('PUT', '/hard-skills/{id:\d+}', ['hardSkill', 'updateHardSkill']);
        $r->addRoute('DELETE', '/hard-skills/{id:\d+}', ['hardSkill', 'deleteHardSkill']);

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

        // Tag routes
        $r->addRoute('GET', '/tags', ['tag', 'getAllTags']);
        $r->addRoute('GET', '/tags/{id:\d+}', ['tag', 'getTagById']);
        $r->addRoute('POST', '/tags', ['tag', 'createTag']);
        $r->addRoute('PUT', '/tags/{id:\d+}', ['tag', 'updateTag']);
        $r->addRoute('DELETE', '/tags/{id:\d+}', ['tag', 'deleteTag']);
        $r->addRoute('GET', '/tags/{tagId:\d+}/articles', ['tag', 'getArticlesByTag']);
        $r->addRoute('GET', '/tags/{tagId:\d+}/services', ['tag', 'getServicesByTag']);
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



        // Provider languages routes
        $r->addRoute('GET', '/providers/{providerId:\d+}/languages', ['provider', 'getProviderLanguages']);
        $r->addRoute('POST', '/providers/{providerId:\d+}/languages/{languageId:\d+}', ['provider', 'addLanguageToProvider']);
        $r->addRoute('DELETE', '/providers/{providerId:\d+}/languages/{languageId:\d+}', ['provider', 'removeLanguageFromProvider']);



        // Upload routes pour les médias
        $r->addRoute('POST', '/providers/{providerId:\d+}/experience/{experienceId:\d+}/logo', ['provider', 'uploadExperienceLogo']);
        $r->addRoute('POST', '/providers/{providerId:\d+}/education/{educationId:\d+}/logo', ['provider', 'uploadEducationLogo']);
        $r->addRoute('POST', '/providers/{providerId:\d+}/completed-works/{workId:\d+}/media', ['provider', 'uploadCompletedWorkMedia']);

        // Provider Image routes
        $r->addRoute('POST', '/providers/{providerId:\d+}/images/profile', ['providerImage', 'uploadProfileImage']);
        $r->addRoute('POST', '/providers/{providerId:\d+}/images/services/{serviceId:\d+}', ['providerImage', 'uploadServiceImage']);
        $r->addRoute('POST', '/providers/{providerId:\d+}/images/articles/{articleId:\d+}', ['providerImage', 'uploadArticleImage']);
        $r->addRoute('POST', '/providers/{providerId:\d+}/images/experiences/{experienceId:\d+}', ['providerImage', 'uploadExperienceImage']);
        $r->addRoute('POST', '/providers/{providerId:\d+}/images/education/{educationId:\d+}', ['providerImage', 'uploadEducationImage']);
        $r->addRoute('GET', '/providers/{providerId:\d+}/images/{imageType}', ['providerImage', 'listImages']);
        $r->addRoute('GET', '/providers/{providerId:\d+}/images/{imageType}/{subId:\d+}', ['providerImage', 'listImagesBySub']);
        $r->addRoute('DELETE', '/providers/{providerId:\d+}/images/{imageType}/{filename}', ['providerImage', 'deleteImage']);
        $r->addRoute('DELETE', '/providers/{providerId:\d+}/images/{imageType}/{subId:\d+}/{filename}', ['providerImage', 'deleteImageBySub']);

        // Client Image routes
        $r->addRoute('POST', '/clients/{clientId:\d+}/images/profile', ['clientImage', 'uploadProfileImage']);
        $r->addRoute('GET', '/clients/{clientId:\d+}/images/profile', ['clientImage', 'listProfileImages']);
        $r->addRoute('DELETE', '/clients/{clientId:\d+}/images/profile/{filename}', ['clientImage', 'deleteProfileImage']);

        // Routes avec slugs pour les URLs SEO-friendly
        $r->addRoute('GET', '/providers/{slug}', ['provider', 'getProviderBySlug']);
        $r->addRoute('GET', '/clients/{slug}', ['client', 'getClientBySlug']);
        // supprimé: GET /services/{slug}
        $r->addRoute('GET', '/articles/{slug}', ['article', 'getArticleBySlug']);

        // Routes imbriquées pour les services et articles d'un provider
        // plus de with-content: renvoyé par défaut
        // variantes ID (utilitaires)
        $r->addRoute('GET', '/providers/{providerId:\d+}/articles', ['article', 'getArticlesByProviderId']);
        $r->addRoute('GET', '/providers/{providerId:\d+}/articles/{articleId:\d+}', ['article', 'getArticleByProviderIdAndArticleId']);
        // plus de with-content: renvoyé par défaut
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
                // Providers
                '/api/v1/providers',
                '/api/v1/providers/{id:\d+}',
                '/api/v1/providers/slug/{slug}',
                '/api/v1/providers/country/{countryId:\d+}',
                '/api/v1/providers/job/{jobId:\d+}',
                '/api/v1/providers/hard-skill/{skillName}',
                '/api/v1/providers/soft-skill/{skillName}',
                '/api/v1/providers/language/{languageName}',
                '/api/v1/providers/search/{query}',
                '/api/v1/providers/{providerSlug}/reviews',
                '/api/v1/providers/{providerSlug}/availability',
                '/api/v1/providers/slug/{providerSlug}/services',
                '/api/v1/providers/slug/{providerSlug}/services/{serviceSlug}',
                '/api/v1/providers/{providerSlug}/services',
                '/api/v1/providers/{providerSlug}/services/{serviceSlug}',
                '/api/v1/providers/slug/{providerSlug}/articles',
                '/api/v1/providers/slug/{providerSlug}/articles/{articleSlug}',
                '/api/v1/providers/{providerSlug}/articles',
                '/api/v1/providers/{providerSlug}/articles/{articleSlug}',
                '/api/v1/providers/slug/{providerSlug}/experiences',
                '/api/v1/providers/slug/{providerSlug}/educations',
                '/api/v1/providers/{slug}',

                // Clients
                '/api/v1/clients',
                '/api/v1/clients/{id:\d+}',
                '/api/v1/clients/email/{email}',
                '/api/v1/clients/slug/{slug}',

                // Services
                '/api/v1/services',
                '/api/v1/services/active',
                '/api/v1/services/featured',
                '/api/v1/services/tag/{tagId:\d+}',
                '/api/v1/services/{slug}',

                // Articles
                '/api/v1/articles',
                '/api/v1/articles/published',
                '/api/v1/articles/featured',
                '/api/v1/articles/tag/{tagId:\d+}',
                '/api/v1/articles/{slug}',

                // Soft skills / Hard skills
                '/api/v1/soft-skills',
                '/api/v1/soft-skills/{id:\d+}',
                '/api/v1/hard-skills',
                '/api/v1/hard-skills/{id:\d+}',

                // Jobs / Languages / Countries
                '/api/v1/jobs',
                '/api/v1/jobs/{id:\d+}',
                '/api/v1/languages',
                '/api/v1/languages/{id:\d+}',
                '/api/v1/countries',
                '/api/v1/countries/{id:\d+}',

                // Tags
                '/api/v1/tags',
                '/api/v1/tags/{id:\d+}',
                '/api/v1/tags/{tagId:\d+}/articles',
                '/api/v1/tags/{tagId:\d+}/services',

                // Completed works
                '/api/v1/completed-works',
                '/api/v1/completed-works/{id:\d+}',

                // Images listing endpoints
                '/api/v1/providers/{providerId:\d+}/images/{imageType}',
                '/api/v1/clients/{clientId:\d+}/images/profile'
            ]
        ];

        // Vérifier si la route actuelle est publique
        $isPublicRoute = false;
        if (isset($publicRoutes[$httpMethod])) {
            foreach ($publicRoutes[$httpMethod] as $publicRoute) {
                // Remplacer tous les placeholders {xxx} par [^/]+, et {id:\d+} par \d+
                $pattern = preg_replace('/\{[^}]+\}/', '[^/]+', $publicRoute);
                $pattern = str_replace('{id:\\d+}', '\\d+', $pattern);
                $pattern = '#^' . $pattern . '$#';
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

        // Appeler la méthode du controller avec les paramètres et retourner la réponse JSON
        if (!empty($data)) {
            $args = array_values($vars);
            $args[] = $data;
            $result = $controller->$action(...$args);
        } else {
            $result = $controller->$action(...array_values($vars));
        }

        if ($result !== null) {
            if (is_array($result) || is_object($result)) {
                echo json_encode($result);
            } else {
                echo (string) $result;
            }
        }
        break;
}
