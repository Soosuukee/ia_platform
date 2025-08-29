<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

class ArticleImage
{
    private int $id;
    private int $articleContentId;
    private string $url;

    public function __construct(int $articleContentId, string $url)
    {
        $this->articleContentId = $articleContentId;
        $this->url = trim($url);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getArticleContentId(): int
    {
        return $this->articleContentId;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'articleContentId' => $this->articleContentId,
            'url' => $this->url,
        ];
    }
}
