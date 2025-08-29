## IaPlatform API – Guide pour le Front

Base URL: `http://localhost:8080/api/v1`

- CORS: `http://localhost:3000`
- Placeholders: `{id}` entier (`{id:\d+}`), autres `{slug}`/`{name}` alphanum.

### Auth

- POST `/auth/login`
- POST `/auth/logout`
- GET `/auth/me`
- POST `/auth/register`
- POST `/auth/change-password`

### Providers

- GET `/providers`
- GET `/providers/{id}`
- GET `/providers/slug/{slug}`
- GET `/providers/country/{countryId}`
- GET `/providers/job/{jobId}`
- GET `/providers/hard-skill/{skillName}`
- GET `/providers/soft-skill/{skillName}`
- GET `/providers/language/{languageName}`
- GET `/providers/search/{query}`
- GET `/providers/{providerSlug}/reviews`
- GET `/providers/{providerSlug}/availability`
- POST `/providers`
- PUT `/providers/{id}`
- PATCH `/providers/{id}`
- DELETE `/providers/{id}`
- POST `/providers/{id}/profile-picture`

### Clients

- GET `/clients`
- GET `/clients/{id}`
- GET `/clients/email/{email}`
- GET `/clients/slug/{slug}`
- POST `/clients`
- PUT `/clients/{id}`
- PATCH `/clients/{id}`
- DELETE `/clients/{id}`

### Services

- GET `/services`
- GET `/services/{id}`
- GET `/providers/slug/{providerSlug}/services`
- GET `/providers/slug/{providerSlug}/services/{serviceSlug}`
- GET `/providers/{providerSlug}/services`
- GET `/providers/{providerSlug}/services/{serviceSlug}`
- GET `/providers/{providerId}/services`
- GET `/services/active`
- GET `/services/featured`
- GET `/services/tag/{tagId}`
- GET `/services/tag/slug/{tagSlug}`
- GET `/services/search/{query}`
- POST `/services`
- PUT `/services/{id}`
- PATCH `/services/{id}`
- DELETE `/services/{id}`
- POST `/services/with-content`
- PATCH `/services/{id}/with-content`
- POST `/services/{id}/cover`

Provider-scoped (médias):

- POST `/providers/{providerId}/services/{serviceId}/cover`
- POST `/providers/{providerId}/services/{serviceId}/sections/{sectionId}/contents/{contentId}/images`
- POST `/providers/{providerId}/{entityType:articles|experiences|education}/{entityId}/images`
- POST `/services/{serviceId}/content/{contentId}/images`
- PUT `/services/slug/{slug}`
- PATCH `/services/slug/{slug}`
- DELETE `/services/slug/{slug}`

### Articles

- GET `/articles`
- GET `/providers/slug/{providerSlug}/articles`
- GET `/providers/slug/{providerSlug}/articles/{articleSlug}``
- GET `/providers/{providerSlug}/articles`
- GET `/providers/{providerSlug}/articles/{articleSlug}`
- GET `/articles/published`
- GET `/articles/featured`
- GET `/articles/tag/{tagId}`
- GET `/articles/tag/slug/{tagSlug}`
- GET `/articles/search/{query}`
- POST `/articles`
- PUT `/articles/{id}`
- PATCH `/articles/{id}`
- PUT `/articles/slug/{slug}`
- PATCH `/articles/slug/{slug}`
- DELETE `/articles/{id}`
- POST `/articles/with-content`
- PATCH `/articles/{id}/with-content`
- POST `/articles/{id}/cover`
- POST `/articles/{articleId}/content/{contentId}/images`
- DELETE `/articles/{articleId}/content/{contentId}/images/{imageId}`
- DELETE `/articles/slug/{slug}`

### Completed Works

- GET `/completed-works`
- GET `/completed-works/{id}`
- POST `/completed-works`
- PATCH `/completed-works/{id}`
- DELETE `/completed-works/{id}`

Uploads liés:

- POST `/providers/{providerId}/completed-works/{workId}/media`

### Soft Skills

- GET `/soft-skills`
- GET `/soft-skills/{id}`
- POST `/soft-skills`
- PUT `/soft-skills/{id}`
- DELETE `/soft-skills/{id}`

### Hard Skills

- GET `/hard-skills`
- GET `/hard-skills/{id}`
- POST `/hard-skills`
- PUT `/hard-skills/{id}`
- DELETE `/hard-skills/{id}`

### Jobs

- GET `/jobs`
- GET `/jobs/{id}`
- POST `/jobs`
- PUT `/jobs/{id}`
- DELETE `/jobs/{id}`

### Languages

- GET `/languages`
- GET `/languages/{id}`
- POST `/languages`
- PUT `/languages/{id}`
- DELETE `/languages/{id}`

### Countries

- GET `/countries`
- GET `/countries/{id}`
- POST `/countries`
- PUT `/countries/{id}`
- DELETE `/countries/{id}`

### Tags

- GET `/tags`
- GET `/tags/{id}`
- POST `/tags`
- PUT `/tags/{id}`
- DELETE `/tags/{id}`
- GET `/tags/{tagId}/articles`
- GET `/tags/{tagId}/services`

### Provider: Skills & Langues

- GET `/providers/{providerId}/soft-skills`
- POST `/providers/{providerId}/soft-skills/{skillId}`
- DELETE `/providers/{providerId}/soft-skills/{skillId}`
- GET `/providers/{providerId}/hard-skills`
- POST `/providers/{providerId}/hard-skills/{skillId}`
- DELETE `/providers/{providerId}/hard-skills/{skillId}`
- GET `/providers/{providerId}/languages`
- POST `/providers/{providerId}/languages/{languageId}`
- DELETE `/providers/{providerId}/languages/{languageId}`

### Provider Images (génériques)

- POST `/providers/{providerId}/images/profile`
- POST `/providers/{providerId}/images/services/{serviceId}`
- POST `/providers/{providerId}/images/articles/{articleId}`
- POST `/providers/{providerId}/images/experiences/{experienceId}`
- POST `/providers/{providerId}/images/education/{educationId}`
- GET `/providers/{providerId}/images/{imageType}`
- GET `/providers/{providerId}/images/{imageType}/{subId}`
- DELETE `/providers/{providerId}/images/{imageType}/{filename}`
- DELETE `/providers/{providerId}/images/{imageType}/{subId}/{filename}`

### Client Images

- POST `/clients/{clientId}/images/profile`
- GET `/clients/{clientId}/images/profile`
- DELETE `/clients/{clientId}/images/profile/{filename}`

### SEO-friendly (slugs)

- GET `/providers/{slug}`
- GET `/clients/{slug}`
- GET `/articles/{slug}`

### Fichiers images statiques (public)

- GET `/images/{...}` (exemples):
  - `/images/providers/{providerId}/profile/profile-picture.jpg`
  - `/images/providers/{providerId}/services/{serviceId}/cover/service-cover.jpg`
  - `/images/providers/{providerId}/articles/{articleId}/article-image-1.jpg`

### Notes

- La plupart des endpoints GET sont publics. Les endpoints d’écriture peuvent exiger une session (cookie PHPSESSID).
