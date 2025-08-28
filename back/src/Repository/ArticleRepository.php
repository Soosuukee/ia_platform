<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Repository;

use Soosuuke\IaPlatform\Entity\Article;
use Soosuuke\IaPlatform\Config\Database;
use ReflectionClass;

class ArticleRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function findById(int $id): ?Article
    {
        $stmt = $this->pdo->prepare('SELECT * FROM article WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        return $data ? $this->mapToArticle($data) : null;
    }

    public function findBySlug(string $slug): ?Article
    {
        $stmt = $this->pdo->prepare('SELECT * FROM article WHERE slug = ?');
        $stmt->execute([$slug]);
        $data = $stmt->fetch();

        return $data ? $this->mapToArticle($data) : null;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM article ORDER BY published_at DESC');
        $articles = [];

        while ($row = $stmt->fetch()) {
            $articles[] = $this->mapToArticle($row);
        }

        return $articles;
    }

    public function findByProviderId(int $providerId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM article WHERE provider_id = ? ORDER BY published_at DESC');
        $stmt->execute([$providerId]);

        $articles = [];
        while ($row = $stmt->fetch()) {
            $articles[] = $this->mapToArticle($row);
        }

        return $articles;
    }

    public function findPublished(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM article WHERE is_published = 1 ORDER BY published_at DESC');
        $articles = [];

        while ($row = $stmt->fetch()) {
            $articles[] = $this->mapToArticle($row);
        }

        return $articles;
    }

    public function findFeatured(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM article WHERE is_featured = 1 AND is_published = 1 ORDER BY published_at DESC');
        $articles = [];

        while ($row = $stmt->fetch()) {
            $articles[] = $this->mapToArticle($row);
        }

        return $articles;
    }

    public function save(Article $article): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO article (provider_id, title, slug, published_at, summary, is_published, is_featured, cover, tag, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');

        $stmt->execute([
            $article->getProviderId(),
            $article->getTitle(),
            $article->getSlug(),
            $article->getPublishedAt()->format('Y-m-d H:i:s'),
            $article->getSummary(),
            $article->isPublished(),
            $article->isFeatured(),
            $article->getCover(),
            $article->getTag(),
            $article->getUpdatedAt()->format('Y-m-d H:i:s')
        ]);

        $id = (int) $this->pdo->lastInsertId();
        $ref = new ReflectionClass(Article::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($article, $id);
    }

    public function update(Article $article): void
    {
        $stmt = $this->pdo->prepare('
            UPDATE article
            SET title = ?, slug = ?, published_at = ?, summary = ?, is_published = ?, is_featured = ?, cover = ?, tag = ?, updated_at = ?
            WHERE id = ?
        ');

        $stmt->execute([
            $article->getTitle(),
            $article->getSlug(),
            $article->getPublishedAt()->format('Y-m-d H:i:s'),
            $article->getSummary(),
            $article->isPublished(),
            $article->isFeatured(),
            $article->getCover(),
            $article->getTag(),
            $article->getUpdatedAt()->format('Y-m-d H:i:s'),
            $article->getId()
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM article WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function deleteByProviderId(int $providerId): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM article WHERE provider_id = ?");
        $stmt->execute([$providerId]);
    }

    public function getArticleWithContent(int $articleId): ?array
    {
        // Récupérer l'article
        $article = $this->findById($articleId);
        if (!$article) {
            return null;
        }

        // Récupérer les sections
        $stmt = $this->pdo->prepare('SELECT * FROM article_section WHERE article_id = ? ORDER BY id');
        $stmt->execute([$articleId]);
        $sections = $stmt->fetchAll();

        $articleData = [
            'article' => $article,
            'sections' => []
        ];

        foreach ($sections as $section) {
            $sectionData = [
                'section' => $section,
                'contents' => []
            ];

            // Récupérer le contenu de chaque section
            $stmt = $this->pdo->prepare('SELECT * FROM article_content WHERE article_content_id = ? ORDER BY id');
            $stmt->execute([$section['id']]);
            $contents = $stmt->fetchAll();

            foreach ($contents as $content) {
                $contentData = [
                    'content' => $content,
                    'images' => []
                ];

                // Récupérer les images de chaque contenu
                $stmt = $this->pdo->prepare('SELECT * FROM article_image WHERE article_content_id = ? ORDER BY id');
                $stmt->execute([$content['id']]);
                $images = $stmt->fetchAll();

                $contentData['images'] = $images;
                $sectionData['contents'][] = $contentData;
            }

            $articleData['sections'][] = $sectionData;
        }

        return $articleData;
    }

    public function saveArticleWithContent(Article $article, array $sections): void
    {
        $this->pdo->beginTransaction();

        try {
            // Sauvegarder l'article
            if ($article->getId()) {
                $this->update($article);
            } else {
                $this->save($article);
            }

            $articleId = $article->getId();

            // Supprimer l'ancien contenu
            $stmt = $this->pdo->prepare('DELETE FROM article_image WHERE article_content_id IN (SELECT id FROM article_content WHERE article_content_id IN (SELECT id FROM article_section WHERE article_id = ?))');
            $stmt->execute([$articleId]);

            $stmt = $this->pdo->prepare('DELETE FROM article_content WHERE article_content_id IN (SELECT id FROM article_section WHERE article_id = ?)');
            $stmt->execute([$articleId]);

            $stmt = $this->pdo->prepare('DELETE FROM article_section WHERE article_id = ?');
            $stmt->execute([$articleId]);

            // Sauvegarder les nouvelles sections
            foreach ($sections as $sectionData) {
                $stmt = $this->pdo->prepare('INSERT INTO article_section (article_id, title) VALUES (?, ?)');
                $stmt->execute([$articleId, $sectionData['title']]);
                $sectionId = (int) $this->pdo->lastInsertId();

                // Sauvegarder le contenu de la section
                if (isset($sectionData['contents'])) {
                    foreach ($sectionData['contents'] as $contentData) {
                        $stmt = $this->pdo->prepare('INSERT INTO article_content (article_content_id, content) VALUES (?, ?)');
                        $stmt->execute([$sectionId, $contentData['content']]);
                        $contentId = (int) $this->pdo->lastInsertId();

                        // Sauvegarder les images du contenu
                        if (isset($contentData['images'])) {
                            foreach ($contentData['images'] as $imageData) {
                                $stmt = $this->pdo->prepare('INSERT INTO article_image (article_content_id, url) VALUES (?, ?)');
                                $stmt->execute([$contentId, $imageData['url']]);
                            }
                        }
                    }
                }
            }

            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    private function mapToArticle(array $data): Article
    {
        $article = new Article(
            (int)$data['provider_id'],
            $data['title'],
            $data['summary'],
            $data['tag'],
            $data['slug'],
            (bool)$data['is_published'],
            (bool)$data['is_featured'],
            $data['cover']
        );

        $ref = new ReflectionClass(Article::class);
        $idProp = $ref->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($article, (int) $data['id']);

        return $article;
    }
}
