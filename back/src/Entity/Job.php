<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

class Job
{
    private int $id;
    private string $title;
    private string $slug;

    public function __construct(string $title, ?string $slug = null)
    {
        $this->title = trim($title);
        $this->slug = $slug ?? '';
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
        ];
    }
}
