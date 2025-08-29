<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Service;

class TagSlugificationService
{
    public function generateTagSlug(string $title, callable $isSlugTaken): string
    {
        $baseSlug = $this->slugify($title);
        $slug = $baseSlug;
        $counter = 1;

        while ($isSlugTaken($slug)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function slugify(string $text): string
    {
        // Convertir en minuscules
        $text = strtolower($text);

        // Remplacer les caractères spéciaux par des espaces
        $text = preg_replace('/[^a-z0-9\s-]/', ' ', $text);

        // Remplacer les espaces multiples par des tirets
        $text = preg_replace('/\s+/', '-', $text);

        // Supprimer les tirets en début et fin
        $text = trim($text, '-');

        return $text;
    }
}
