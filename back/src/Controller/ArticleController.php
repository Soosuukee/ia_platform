<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Controller;

use Soosuuke\IaPlatform\Repository\ArticleRepository;
use Soosuuke\IaPlatform\Repository\ProviderRepository;
use Soosuuke\IaPlatform\Entity\Article;
use Soosuuke\IaPlatform\Service\ArticleSlugificationService;
use Soosuuke\IaPlatform\Service\FileUploadService;
use Soosuuke\IaPlatform\Config\AuthMiddleware;

class ArticleController
{
    private ArticleRepository $articleRepository;
    private ProviderRepository $providerRepository;
    private ArticleSlugificationService $slugificationService;
    private FileUploadService $fileUploadService;

    public function __construct()
    {
        $this->articleRepository = new ArticleRepository();
        $this->providerRepository = new ProviderRepository();
        $this->slugificationService = new ArticleSlugificationService();
        $this->fileUploadService = new FileUploadService();
    }

    // GET /articles
    public function getAllArticles(): array
    {
        $articles = $this->articleRepository->findAll();
        return array_map(function (Article $article) {
            return $article->toArray();
        }, $articles);
    }

    // GET /articles/{id}
    public function getArticleById(int $id): ?array
    {
        return $this->articleRepository->getArticleWithContent($id);
    }

    // GET /articles/slug/{slug}
    public function getArticleBySlug(string $slug): array
    {
        // Retourner la liste des articles filtrés par slug (pattern)
        // Ici, on renvoie un tableau d'articles correspondants (complet via getArticleWithContent)
        $found = $this->articleRepository->findBySlug($slug);
        if (!$found) {
            return [];
        }
        return $this->articleRepository->getArticleWithContent($found->getId());
    }

    // GET /articles/slug/{slug}/with-content
    public function getArticleBySlugWithContent(string $slug): ?array
    {
        $article = $this->articleRepository->findBySlug($slug);
        if (!$article) {
            return null;
        }
        return $this->articleRepository->getArticleWithContent($article->getId());
    }

    // GET /articles/provider/{providerSlug}
    public function getArticlesByProviderSlug(string $providerSlug): array
    {
        $articles = $this->articleRepository->findByProviderSlug($providerSlug);

        // Retourner les articles avec leur contenu complet
        $articlesWithContent = [];
        foreach ($articles as $article) {
            $articlesWithContent[] = $this->articleRepository->getArticleWithContent($article->getId());
        }

        return $articlesWithContent;
    }

    // GET /articles/published
    public function getPublishedArticles(): array
    {
        $articles = $this->articleRepository->findPublished();
        return array_map(fn(Article $a) => $a->toArray(), $articles);
    }

    // GET /articles/featured
    public function getFeaturedArticles(): array
    {
        $articles = $this->articleRepository->findFeatured();
        return array_map(fn(Article $a) => $a->toArray(), $articles);
    }

    // POST /articles
    public function createArticle(array $data): Article
    {
        // Générer le slug automatiquement basé sur le titre
        $slug = $this->slugificationService->generateArticleSlug(
            $data['title'],
            function ($slug) {
                return $this->articleRepository->findBySlug($slug) !== null;
            }
        );

        $article = new Article(
            (int) $data['providerId'],
            (int) ($data['languageId'] ?? 1), // Default language ID
            $data['title'],
            $data['summary'] ?? '',
            $slug,
            $data['isPublished'] ?? false,
            $data['isFeatured'] ?? false,
            $data['cover'] ?? null
        );

        $this->articleRepository->save($article);
        return $article;
    }

    // GET /providers/{providerSlug}/articles/{articleSlug}
    public function getArticleByProviderAndSlug(string $providerSlug, string $articleSlug): ?array
    {
        // D'abord trouver le provider par son slug
        $provider = $this->providerRepository->findBySlug($providerSlug);
        if (!$provider) {
            return null;
        }

        // Ensuite trouver l'article par son slug et le provider ID
        $article = $this->articleRepository->findBySlug($articleSlug);
        if (!$article || $article->getProviderId() !== $provider->getId()) {
            return null;
        }

        return $this->articleRepository->getArticleWithContent($article->getId());
    }



    // PUT /articles/{id}
    public function updateArticle(int $id, array $data): ?Article
    {
        $article = $this->articleRepository->findById($id);
        if (!$article) {
            return null;
        }

        // Security: only owner provider can update
        $currentUserId = AuthMiddleware::getCurrentUserId();
        $currentUserType = AuthMiddleware::getCurrentUserType();
        if ($currentUserType !== 'provider' || $article->getProviderId() !== $currentUserId) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Accès interdit']);
            return null;
        }

        // Mise à jour des propriétés
        $article = new Article(
            $data['providerId'] ?? $article->getProviderId(),
            $data['languageId'] ?? $article->getLanguageId(),
            $data['title'] ?? $article->getTitle(),
            $data['summary'] ?? $article->getSummary(),
            $data['slug'] ?? $article->getSlug(),
            $data['isPublished'] ?? $article->isPublished(),
            $data['isFeatured'] ?? $article->isFeatured(),
            $data['cover'] ?? $article->getCover()
        );

        $this->articleRepository->update($article);
        return $article;
    }

    // DELETE /articles/{id}
    public function deleteArticle(int $id): bool
    {
        $article = $this->articleRepository->findById($id);
        if (!$article) {
            return false;
        }

        $this->articleRepository->delete($id);
        return true;
    }

    // PUT/PATCH /articles/slug/{slug}
    public function updateArticleBySlug(string $slug, array $data): ?Article
    {
        $article = $this->articleRepository->findBySlug($slug);
        if (!$article) {
            return null;
        }

        $currentUserId = AuthMiddleware::getCurrentUserId();
        $currentUserType = AuthMiddleware::getCurrentUserType();
        if ($currentUserType !== 'provider' || $article->getProviderId() !== $currentUserId) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Accès interdit']);
            return null;
        }

        $article = new Article(
            $data['providerId'] ?? $article->getProviderId(),
            $data['languageId'] ?? $article->getLanguageId(),
            $data['title'] ?? $article->getTitle(),
            $data['summary'] ?? $article->getSummary(),
            $data['slug'] ?? $article->getSlug(),
            $data['isPublished'] ?? $article->isPublished(),
            $data['isFeatured'] ?? $article->isFeatured(),
            $data['cover'] ?? $article->getCover()
        );

        $this->articleRepository->update($article);
        return $article;
    }

    // DELETE /articles/slug/{slug}
    public function deleteArticleBySlug(string $slug): bool
    {
        $article = $this->articleRepository->findBySlug($slug);
        if (!$article) {
            return false;
        }

        $currentUserId = AuthMiddleware::getCurrentUserId();
        $currentUserType = AuthMiddleware::getCurrentUserType();
        if ($currentUserType !== 'provider' || $article->getProviderId() !== $currentUserId) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Accès interdit']);
            return false;
        }

        $this->articleRepository->delete($article->getId());
        return true;
    }

    // GET /articles/{id}/with-content
    public function getArticleWithContent(int $id): ?array
    {
        return $this->articleRepository->getArticleWithContent($id);
    }

    // POST /articles/with-content
    public function createArticleWithContent(array $data): ?Article
    {
        $article = new Article(
            (int) $data['providerId'],
            (int) ($data['languageId'] ?? 1), // Default language ID
            $data['title'],
            $data['summary'] ?? '',
            $data['slug'] ?? null,
            $data['isPublished'] ?? false,
            $data['isFeatured'] ?? false,
            $data['cover'] ?? null
        );

        $sections = $data['sections'] ?? [];

        $this->articleRepository->saveArticleWithContent($article, $sections);
        return $article;
    }

    // PATCH /articles/{id}/with-content
    public function patchArticleWithContent(int $id, array $data): ?Article
    {
        $article = $this->articleRepository->findById($id);
        if (!$article) {
            return null;
        }

        // Security: only owner provider can patch
        $currentUserId = AuthMiddleware::getCurrentUserId();
        $currentUserType = AuthMiddleware::getCurrentUserType();
        if ($currentUserType !== 'provider' || $article->getProviderId() !== $currentUserId) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Accès interdit']);
            return null;
        }

        // Mettre à jour quelques métadonnées si fournies
        if (isset($data['title'])) {
            $article->setTitle($data['title']);
        }
        if (isset($data['summary'])) {
            $article->setSummary($data['summary']);
        }

        if (isset($data['isPublished'])) {
            $article->setIsPublished((bool)$data['isPublished']);
        }
        if (isset($data['isFeatured'])) {
            $article->setIsFeatured((bool)$data['isFeatured']);
        }
        if (isset($data['cover'])) {
            $article->setCover($data['cover']);
        }

        $sections = $data['sections'] ?? [];
        $this->articleRepository->saveArticleWithContent($article, $sections);

        return $article;
    }

    // POST /articles/{id}/cover
    public function uploadCover(int $articleId, array $file): array
    {
        try {
            $article = $this->articleRepository->findById($articleId);
            if (!$article) {
                return [
                    'success' => false,
                    'message' => 'Article non trouvé'
                ];
            }

            // Security: only owner provider can update cover
            $currentUserId = AuthMiddleware::getCurrentUserId();
            $currentUserType = AuthMiddleware::getCurrentUserType();
            if ($currentUserType !== 'provider' || $article->getProviderId() !== $currentUserId) {
                return [
                    'success' => false,
                    'message' => 'Accès interdit'
                ];
            }

            if ($article->getCover()) {
                $this->fileUploadService->deleteFile($article->getCover());
            }

            $newCoverUrl = $this->fileUploadService->uploadArticleCover(
                $file,
                $articleId
            );

            $article->setCover($newCoverUrl);
            $this->articleRepository->update($article);

            return [
                'success' => true,
                'message' => 'Couverture mise à jour avec succès',
                'cover' => $newCoverUrl
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'upload: ' . $e->getMessage()
            ];
        }
    }

    // POST /articles/{articleId}/content/{contentId}/images
    public function uploadImage(int $articleId, int $contentId, array $file): array
    {
        try {
            $article = $this->articleRepository->findById($articleId);
            if (!$article) {
                return [
                    'success' => false,
                    'message' => 'Article non trouvé'
                ];
            }

            $newImageUrl = $this->fileUploadService->uploadArticleImage(
                $file,
                $contentId
            );

            return [
                'success' => true,
                'message' => 'Image uploadée avec succès',
                'image' => $newImageUrl
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'upload: ' . $e->getMessage()
            ];
        }
    }

    public function getArticlesByTag(int $tagId): array
    {
        $articles = $this->articleRepository->findByTagId($tagId);
        return array_map(fn(Article $article) => [
            'id' => $article->getId(),
            'title' => $article->getTitle(),
            'summary' => $article->getSummary(),
            'slug' => $article->getSlug(),
            'isPublished' => $article->isPublished(),
            'isFeatured' => $article->isFeatured(),
            'cover' => $article->getCover(),
            'publishedAt' => $article->getPublishedAt()->format('Y-m-d H:i:s')
        ], $articles);
    }
}
