<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Controller;

use Soosuuke\IaPlatform\Repository\ArticleRepository;
use Soosuuke\IaPlatform\Repository\ProviderRepository;
use Soosuuke\IaPlatform\Entity\Article;
use Soosuuke\IaPlatform\Service\ArticleSlugificationService;
use Soosuuke\IaPlatform\Service\FileUploadService;

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
        return $this->articleRepository->findAll();
    }

    // GET /articles/{id}
    public function getArticleById(int $id): ?Article
    {
        return $this->articleRepository->findById($id);
    }

    // GET /articles/slug/{slug}
    public function getArticleBySlug(string $slug): ?Article
    {
        return $this->articleRepository->findBySlug($slug);
    }

    // GET /articles/provider/{providerId}
    public function getArticlesByProviderId(int $providerId): array
    {
        return $this->articleRepository->findByProviderId($providerId);
    }

    // GET /articles/published
    public function getPublishedArticles(): array
    {
        return $this->articleRepository->findPublished();
    }

    // GET /articles/featured
    public function getFeaturedArticles(): array
    {
        return $this->articleRepository->findFeatured();
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
            $data['title'],
            $data['summary'] ?? '',
            $data['tag'] ?? '',
            $slug,
            $data['isPublished'] ?? false,
            $data['isFeatured'] ?? false,
            $data['cover'] ?? null
        );

        $this->articleRepository->save($article);
        return $article;
    }

    // GET /providers/{providerSlug}/articles/{articleSlug}
    public function getArticleByProviderAndSlug(string $providerSlug, string $articleSlug): ?Article
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

        return $article;
    }

    // PUT /articles/{id}
    public function updateArticle(int $id, array $data): ?Article
    {
        $article = $this->articleRepository->findById($id);
        if (!$article) {
            return null;
        }

        // Mise à jour des propriétés
        $article = new Article(
            $data['providerId'] ?? $article->getProviderId(),
            $data['title'] ?? $article->getTitle(),
            $data['summary'] ?? $article->getSummary(),
            $data['tag'] ?? $article->getTag(),
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
            $data['title'],
            $data['summary'] ?? '',
            $data['tag'] ?? '',
            $data['slug'] ?? null,
            $data['isPublished'] ?? false,
            $data['isFeatured'] ?? false,
            $data['cover'] ?? null
        );

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
}
