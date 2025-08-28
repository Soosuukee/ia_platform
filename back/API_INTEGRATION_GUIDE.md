# Guide d'Intégration API - skAi Frontend

## Vue d'ensemble

Ce guide explique comment transformer une API existante pour qu'elle soit compatible avec le frontend Next.js de skAi. Il se base sur la structure de données et les interfaces TypeScript existantes.

## Structure des Données Actuelles

### Entités Principales

#### 1. Services (`/api/services`)

```typescript
interface Service {
  serviceId: number;
  providerId: number;
  title: string;
  description: string;
  slug: string;
  isActive: boolean;
  isFeatured: boolean;
  minPrice: number | null;
  maxPrice: number | null;
  estimatedDuration: string;
  availability: string;
  responseTime: string;
  rating?: number;
  reviewCount?: number;
  completedProjects?: number;
  createdAt: string;
  updatedAt?: string;
}
```

#### 2. Providers (`/api/providers`)

```typescript
interface Provider {
  providerId: number;
  slug: string;
  firstName: string;
  lastName: string;
  email: string;
  avatar: string;
  location: Location;
  createdAt: string;
}

interface ProviderWithDetails extends Provider {
  skills: Skill[];
  experiences: Experience[];
  education: Education[];
  socialLinks: SocialLink[];
  services: Service[];
  articles: Article[];
  job: Job;
  languages: Language[];
}
```

#### 3. Articles (`/api/articles`)

```typescript
interface Article {
  articleId: number;
  providerId: number;
  slug: string;
  title: string;
  summary: string;
  publishedAt: string;
  articleCover: string;
  tag: string;
  language: string;
  isPublished: boolean;
  section: ArticleSection[];
}
```

## Endpoints API Requis

### 1. Services

```
GET    /api/services                    # Liste tous les services
GET    /api/services/{id}              # Détails d'un service
GET    /api/services/provider/{id}     # Services d'un provider
POST   /api/services                   # Créer un service
PUT    /api/services/{id}              # Modifier un service
DELETE /api/services/{id}              # Supprimer un service
```

### 2. Providers

```
GET    /api/providers                  # Liste tous les providers
GET    /api/providers/{slug}           # Détails d'un provider
GET    /api/providers/{slug}/services  # Services d'un provider
GET    /api/providers/{slug}/articles  # Articles d'un provider
POST   /api/providers                  # Créer un provider
PUT    /api/providers/{id}             # Modifier un provider
DELETE /api/providers/{id}             # Supprimer un provider
```

### 3. Articles

```
GET    /api/articles                   # Liste tous les articles
GET    /api/articles/{slug}            # Détails d'un article
GET    /api/articles/provider/{id}     # Articles d'un provider
POST   /api/articles                   # Créer un article
PUT    /api/articles/{id}              # Modifier un article
DELETE /api/articles/{id}              # Supprimer un article
```

### 4. Authentification

```
POST   /api/authenticate              # Connexion
POST   /api/register                  # Inscription
POST   /api/logout                    # Déconnexion
GET    /api/check-auth                # Vérifier l'authentification
```

## Format des Réponses

### Réponse Succès Standard

```json
{
  "success": true,
  "data": {
    // Données de l'entité
  },
  "message": "Opération réussie"
}
```

### Réponse Erreur Standard

```json
{
  "success": false,
  "error": "Message d'erreur",
  "code": "ERROR_CODE"
}
```

### Réponse Liste avec Pagination

```json
{
  "success": true,
  "data": [
    // Liste des entités
  ],
  "pagination": {
    "page": 1,
    "limit": 10,
    "total": 100,
    "totalPages": 10
  }
}
```

## Headers Requis

### Authentification

```http
Authorization: Bearer {JWT_TOKEN}
```

### Content-Type

```http
Content-Type: application/json
```

### CORS

```http
Access-Control-Allow-Origin: *
Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS
Access-Control-Allow-Headers: Content-Type, Authorization
```

## Paramètres de Requête

### Filtrage

```
GET /api/services?isActive=true&isFeatured=true
GET /api/providers?location=Paris&skills=1,2,3
GET /api/articles?tag=AI&language=fr
```

### Tri

```
GET /api/services?sortBy=createdAt&sortOrder=desc
GET /api/providers?sortBy=firstName&sortOrder=asc
GET /api/articles?sortBy=publishedAt&sortOrder=desc
```

### Pagination

```
GET /api/services?page=1&limit=10
GET /api/providers?page=2&limit=20
GET /api/articles?page=1&limit=15
```

### Recherche

```
GET /api/services?search=machine learning
GET /api/providers?search=Jean Dupont
GET /api/articles?search=intelligence artificielle
```

## Gestion des Images

### Upload d'Images

```
POST /api/upload/image
Content-Type: multipart/form-data

{
  "file": [fichier],
  "type": "service|provider|article",
  "entityId": 123
}
```

### Réponse Upload

```json
{
  "success": true,
  "data": {
    "imageUrl": "/images/services/uploaded-image.jpg",
    "altText": "Description automatique",
    "caption": "Légende optionnelle"
  }
}
```

## Codes d'Erreur

### Codes HTTP

- `200` - Succès
- `201` - Créé avec succès
- `400` - Requête invalide
- `401` - Non authentifié
- `403` - Non autorisé
- `404` - Ressource non trouvée
- `422` - Données invalides
- `500` - Erreur serveur

### Codes d'Erreur Métier

```json
{
  "VALIDATION_ERROR": "Données de validation invalides",
  "AUTHENTICATION_FAILED": "Échec d'authentification",
  "RESOURCE_NOT_FOUND": "Ressource non trouvée",
  "PERMISSION_DENIED": "Permission refusée",
  "DUPLICATE_ENTRY": "Entrée en double",
  "UPLOAD_FAILED": "Échec de l'upload"
}
```

## Exemples de Requêtes

### Créer un Service

```bash
curl -X POST http://api.skai.com/api/services \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "providerId": 1,
    "title": "Développement IA",
    "description": "Services de développement en IA",
    "minPrice": 1000,
    "maxPrice": 5000,
    "estimatedDuration": "2-4 semaines",
    "availability": "Disponible"
  }'
```

### Récupérer un Provider avec Détails

```bash
curl -X GET http://api.skai.com/api/providers/jean-dupont \
  -H "Authorization: Bearer {token}"
```

### Lister les Articles avec Filtres

```bash
curl -X GET "http://api.skai.com/api/articles?tag=AI&language=fr&page=1&limit=10" \
  -H "Authorization: Bearer {token}"
```

## Migration depuis l'API Actuelle

### 1. Modifier les URLs

```javascript
// Avant (API Next.js locale)
const response = await fetch("/api/services");

// Après (API externe)
const response = await fetch("http://api.skai.com/api/services");
```

### 2. Ajouter l'Authentification

```javascript
// Ajouter le token dans les headers
const headers = {
  "Content-Type": "application/json",
  Authorization: `Bearer ${token}`,
};

const response = await fetch("/api/services", { headers });
```

### 3. Gérer les Erreurs

```javascript
try {
  const response = await fetch("/api/services");
  const data = await response.json();

  if (!data.success) {
    throw new Error(data.error);
  }

  return data.data;
} catch (error) {
  console.error("Erreur API:", error);
  throw error;
}
```

## Variables d'Environnement

### Frontend (.env.local)

```env
NEXT_PUBLIC_API_URL=http://api.skai.com
NEXT_PUBLIC_API_VERSION=v1
```

### Backend (.env)

```env
JWT_SECRET=your-secret-key
DATABASE_URL=mysql://user:pass@localhost/skai
CORS_ORIGIN=http://localhost:3000
```

## Tests d'Intégration

### Endpoint de Santé

```
GET /api/health
```

### Réponse

```json
{
  "success": true,
  "data": {
    "status": "healthy",
    "timestamp": "2024-01-01T00:00:00Z",
    "version": "1.0.0"
  }
}
```

## Sécurité

### Validation des Données

- Valider tous les inputs côté serveur
- Sanitizer les données avant stockage
- Utiliser des schémas de validation (Joi, Zod)

### Authentification

- JWT tokens avec expiration
- Refresh tokens pour la sécurité
- Rate limiting sur les endpoints sensibles

### CORS

- Configurer les origines autorisées
- Limiter les méthodes HTTP
- Gérer les preflight requests

## Performance

### Cache

```http
Cache-Control: public, s-maxage=3600, stale-while-revalidate=86400
```

### Compression

```http
Content-Encoding: gzip
```

### Pagination

- Limiter le nombre d'éléments par page
- Utiliser des cursors pour les grandes listes
- Implémenter la pagination côté serveur

## Monitoring

### Logs

- Logger toutes les requêtes API
- Tracer les erreurs avec stack traces
- Monitorer les performances

### Métriques

- Temps de réponse moyen
- Taux d'erreur
- Utilisation des ressources

## Déploiement

### Environnements

- `development` - Développement local
- `staging` - Tests et validation
- `production` - Environnement de production

### Configuration

- Variables d'environnement par environnement
- Base de données séparées
- URLs d'API différentes

## Support

Pour toute question sur l'intégration :

- Consulter la documentation de l'API
- Vérifier les logs d'erreur
- Tester avec Postman/Insomnia
- Contacter l'équipe backend
