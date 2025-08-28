# Guide d'Authentification et Protection des Routes

## Vue d'ensemble

Le système d'authentification utilise des sessions PHP pour gérer l'état de connexion des utilisateurs. Un middleware d'authentification a été implémenté pour protéger les routes de l'API.

## Routes Publiques vs Protégées

### Routes Publiques (pas d'authentification requise)

**Authentification :**

- `POST /api/v1/auth/login` - Connexion
- `POST /api/v1/auth/register` - Inscription

**Lecture publique :**

- `GET /api/v1/providers` - Liste des providers
- `GET /api/v1/providers/{id}` - Profil d'un provider
- `GET /api/v1/providers/slug/{slug}` - Profil par slug
- `GET /api/v1/services` - Liste des services
- `GET /api/v1/services/{id}` - Détails d'un service
- `GET /api/v1/services/slug/{slug}` - Service par slug
- `GET /api/v1/services/active` - Services actifs
- `GET /api/v1/services/featured` - Services en vedette
- `GET /api/v1/articles` - Liste des articles
- `GET /api/v1/articles/{id}` - Détails d'un article
- `GET /api/v1/articles/slug/{slug}` - Article par slug
- `GET /api/v1/articles/published` - Articles publiés
- `GET /api/v1/articles/featured` - Articles en vedette
- `GET /api/v1/skills` - Compétences
- `GET /api/v1/jobs` - Métiers
- `GET /api/v1/languages` - Langues
- `GET /api/v1/countries` - Pays

### Routes Protégées (authentification requise)

Toutes les autres routes nécessitent une authentification, notamment :

- Création, modification, suppression de ressources
- Upload de fichiers
- Gestion des profils utilisateur
- Actions spécifiques aux providers/clients

## Utilisation du Middleware

### Dans les Contrôleurs

```php
use Soosuuke\IaPlatform\Config\AuthMiddleware;

class MonController
{
    public function maMethode()
    {
        // Vérifier si l'utilisateur est connecté
        if (!AuthMiddleware::isAuthenticated()) {
            return ['success' => false, 'message' => 'Non connecté'];
        }

        // Vérifier le type d'utilisateur
        if (AuthMiddleware::isProvider()) {
            // Logique pour les providers
        }

        if (AuthMiddleware::isClient()) {
            // Logique pour les clients
        }

        // Récupérer l'ID de l'utilisateur connecté
        $userId = AuthMiddleware::getCurrentUserId();
        $userType = AuthMiddleware::getCurrentUserType();

        // Vérifier l'accès à une ressource
        if (AuthMiddleware::canAccessResource($resourceUserId, $resourceUserType)) {
            // Accès autorisé
        }
    }
}
```

### Méthodes Disponibles

- `AuthMiddleware::isAuthenticated()` - Vérifie si l'utilisateur est connecté
- `AuthMiddleware::isProvider()` - Vérifie si l'utilisateur est un provider
- `AuthMiddleware::isClient()` - Vérifie si l'utilisateur est un client
- `AuthMiddleware::getCurrentUserId()` - Retourne l'ID de l'utilisateur connecté
- `AuthMiddleware::getCurrentUserType()` - Retourne le type d'utilisateur
- `AuthMiddleware::canAccessResource($resourceUserId, $resourceUserType)` - Vérifie l'accès à une ressource

## Gestion des Erreurs

### Code 401 - Non authentifié

```json
{
  "success": false,
  "message": "Authentification requise"
}
```

### Code 403 - Accès interdit

```json
{
  "success": false,
  "message": "Accès réservé aux providers"
}
```

## Exemples d'Utilisation

### Protection d'une Route Provider

```php
public function createService(array $data): array
{
    if (!AuthMiddleware::isProvider()) {
        return [
            'success' => false,
            'message' => 'Seuls les providers peuvent créer des services'
        ];
    }

    // Logique de création de service
}
```

### Protection d'une Ressource Utilisateur

```php
public function updateProfile(int $userId, array $data): array
{
    $currentUserId = AuthMiddleware::getCurrentUserId();

    if ($currentUserId !== $userId) {
        return [
            'success' => false,
            'message' => 'Vous ne pouvez modifier que votre propre profil'
        ];
    }

    // Logique de mise à jour
}
```

## Sécurité

- Les sessions sont gérées côté serveur
- Les mots de passe sont hashés avec `password_hash()`
- Vérification automatique de l'authentification sur toutes les routes protégées
- Séparation des rôles providers/clients
- Protection contre l'accès non autorisé aux ressources
