<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Controller;

use Soosuuke\IaPlatform\Entity\Tag;
use Soosuuke\IaPlatform\Repository\TagRepository;

class TagController
{
    private TagRepository $tagRepository;

    public function __construct()
    {
        $this->tagRepository = new TagRepository();
    }

    public function getAllTags(): array
    {
        $tags = $this->tagRepository->findAll();
        return array_map(fn(Tag $tag) => [
            'id' => $tag->getId(),
            'title' => $tag->getTitle()
        ], $tags);
    }

    public function getTagById(int $id): ?array
    {
        $tag = $this->tagRepository->findById($id);
        if (!$tag) {
            return null;
        }

        return [
            'id' => $tag->getId(),
            'title' => $tag->getTitle()
        ];
    }

    public function createTag(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['title']) || empty(trim($data['title']))) {
            http_response_code(400);
            return ['error' => 'Title is required'];
        }

        $tag = new Tag(trim($data['title']));
        $this->tagRepository->save($tag);

        http_response_code(201);
        return [
            'id' => $tag->getId(),
            'title' => $tag->getTitle()
        ];
    }

    public function updateTag(int $id): array
    {
        $tag = $this->tagRepository->findById($id);
        if (!$tag) {
            http_response_code(404);
            return ['error' => 'Tag not found'];
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['title']) || empty(trim($data['title']))) {
            http_response_code(400);
            return ['error' => 'Title is required'];
        }

        $tag->setTitle(trim($data['title']));
        $this->tagRepository->update($tag);

        return [
            'id' => $tag->getId(),
            'title' => $tag->getTitle()
        ];
    }

    public function deleteTag(int $id): array
    {
        $tag = $this->tagRepository->findById($id);
        if (!$tag) {
            http_response_code(404);
            return ['error' => 'Tag not found'];
        }

        $this->tagRepository->delete($id);
        return ['message' => 'Tag deleted successfully'];
    }

    public function getArticlesByTag(int $tagId): array
    {
        $articleRepository = new \Soosuuke\IaPlatform\Repository\ArticleRepository();
        $articles = $articleRepository->findByTagId($tagId);
        return array_map(fn(\Soosuuke\IaPlatform\Entity\Article $article) => [
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

    public function getServicesByTag(int $tagId): array
    {
        $serviceRepository = new \Soosuuke\IaPlatform\Repository\ServiceRepository();
        $services = $serviceRepository->findByTagId($tagId);
        return array_map(fn(\Soosuuke\IaPlatform\Entity\Service $service) => [
            'id' => $service->getId(),
            'providerId' => $service->getProviderId(),
            'summary' => $service->getSummary(),
            'maxPrice' => $service->getMaxPrice(),
            'minPrice' => $service->getMinPrice(),
            'isActive' => $service->isActive(),
            'isFeatured' => $service->isFeatured(),
            'cover' => $service->getCover(),
            'slug' => $service->getSlug(),
            'createdAt' => $service->getCreatedAt()->format('Y-m-d H:i:s')
        ], $services);
    }
}
