<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

class ArticleTag
{
    private int $id;
    private int $articleId;
    private int $tagId;

    public function __construct(int $articleId, int $tagId, ?int $id = null)
    {
        $this->articleId = $articleId;
        $this->tagId = $tagId;
        if ($id !== null) {
            $this->id = $id;
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getArticleId(): int
    {
        return $this->articleId;
    }

    public function getTagId(): int
    {
        return $this->tagId;
    }
}
