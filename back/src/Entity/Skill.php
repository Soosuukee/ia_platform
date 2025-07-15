<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

class Skill
{
    private int $id;
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    // Setters
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
