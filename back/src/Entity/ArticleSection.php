<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

class ArticleSection
{
    private int $id;
    private int $articleId;
    private string $title;

    public function __construct(int $articleId, string $title)
    {
        $this->articleId = $articleId;
        $this->title = trim($title);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getArticleId(): int
    {
        return $this->articleId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
