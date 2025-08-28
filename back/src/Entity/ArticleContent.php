<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

class ArticleContent
{
    private int $id;
    private int $articleContentId;
    private string $content;

    public function __construct(int $articleContentId, string $content)
    {
        $this->articleContentId = $articleContentId;
        $this->content = trim($content);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getArticleContentId(): int
    {
        return $this->articleContentId;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
