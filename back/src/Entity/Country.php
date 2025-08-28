<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

class Country
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
}
