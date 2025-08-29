<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

class ArticleContent
{
    private int $id;
    private int $articleSectionId;
    private string $content;

    public function __construct(int $articleSectionId, string $content)
    {
        $this->articleSectionId = $articleSectionId;
        $this->content = trim($content);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getArticleSectionId(): int
    {
        return $this->articleSectionId;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'articleSectionId' => $this->articleSectionId,
            'content' => $this->content,
        ];
    }
}
