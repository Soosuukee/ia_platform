<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

class Language
{
    private int $id;
    private string $name;

    public function __construct(string $name)
    {
        $this->name = trim($name);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
