<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity\ValueObject;

class Rating
{
    private int $value;

    public function __construct(int $rating)
    {
        if ($rating < 1 || $rating > 5) {
            throw new \InvalidArgumentException('La note doit être comprise entre 1 et 5');
        }

        $this->value = $rating;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getStars(): string
    {
        return str_repeat('★', $this->value) . str_repeat('☆', 5 - $this->value);
    }

    public function equals(Rating $other): bool
    {
        return $this->value === $other->value;
    }

    public function isGreaterThan(Rating $other): bool
    {
        return $this->value > $other->value;
    }

    public function isLessThan(Rating $other): bool
    {
        return $this->value < $other->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
