<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

class HardSkill
{
    private int $id;
    private string $title;

    public function __construct(string $title)
    {
        $this->title = trim($title);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
        ];
    }
}
