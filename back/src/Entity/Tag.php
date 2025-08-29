<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

class Tag
{
    private int $id;
    private string $title;
    private string $slug;

    public function __construct(string $title, ?int $id = null, ?string $slug = null)
    {
        $this->title = trim($title);
        $this->slug = $slug ?? $this->generateSlug($title);
        if ($id !== null) {
            $this->id = $id;
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = trim($title);
        $this->slug = $this->generateSlug($title);
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
            'id' => $this->id ?? 0,
            'title' => $this->title,
            'slug' => $this->slug,
        ];
    }

    private function generateSlug(string $title): string
    {
        // Convertir en minuscules et remplacer les espaces par des tirets
        $slug = strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        return trim($slug, '-');
    }
}
