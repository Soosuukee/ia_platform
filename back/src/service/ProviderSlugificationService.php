<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Service;

class ProviderSlugificationService
{
    /**
     * Génère un slug à partir d'une chaîne de caractères
     */
    public function slugify(string $text): string
    {
        // Convertir en minuscules
        $text = mb_strtolower($text, 'UTF-8');

        // Remplacer les caractères accentués
        $text = $this->removeAccents($text);

        // Remplacer les espaces et caractères spéciaux par des tirets
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s-]+/', '-', $text);

        // Supprimer les tirets en début et fin
        $text = trim($text, '-');

        // Limiter la longueur
        if (strlen($text) > 100) {
            $text = substr($text, 0, 100);
            $text = rtrim($text, '-');
        }

        return $text;
    }

    /**
     * Supprime les accents des caractères
     */
    private function removeAccents(string $string): string
    {
        $search = [
            'à',
            'á',
            'â',
            'ã',
            'ä',
            'å',
            'æ',
            'ç',
            'è',
            'é',
            'ê',
            'ë',
            'ì',
            'í',
            'î',
            'ï',
            'ð',
            'ñ',
            'ò',
            'ó',
            'ô',
            'õ',
            'ö',
            'ø',
            'ù',
            'ú',
            'û',
            'ü',
            'ý',
            'þ',
            'ÿ',
            'À',
            'Á',
            'Â',
            'Ã',
            'Ä',
            'Å',
            'Æ',
            'Ç',
            'È',
            'É',
            'Ê',
            'Ë',
            'Ì',
            'Í',
            'Î',
            'Ï',
            'Ð',
            'Ñ',
            'Ò',
            'Ó',
            'Ô',
            'Õ',
            'Ö',
            'Ø',
            'Ù',
            'Ú',
            'Û',
            'Ü',
            'Ý',
            'Þ',
            'Ÿ'
        ];

        $replace = [
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'ae',
            'c',
            'e',
            'e',
            'e',
            'e',
            'i',
            'i',
            'i',
            'i',
            'o',
            'n',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'u',
            'u',
            'u',
            'u',
            'y',
            'th',
            'y',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'ae',
            'c',
            'e',
            'e',
            'e',
            'e',
            'i',
            'i',
            'i',
            'i',
            'o',
            'n',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'u',
            'u',
            'u',
            'u',
            'y',
            'th',
            'y'
        ];

        return str_replace($search, $replace, $string);
    }

    /**
     * Génère une lettre aléatoire
     */
    private function generateRandomLetter(): string
    {
        $letters = 'abcdefghijklmnopqrstuvwxyz';
        return $letters[rand(0, strlen($letters) - 1)];
    }

    /**
     * Génère 10 chiffres aléatoires
     */
    private function generateRandomDigits(): string
    {
        $digits = '';
        for ($i = 0; $i < 10; $i++) {
            $digits .= rand(0, 9);
        }
        return $digits;
    }

    /**
     * Génère un slug unique en ajoutant un suffixe aléatoire si nécessaire
     */
    public function generateUniqueSlug(string $baseText, callable $checkExists, int $maxAttempts = 10): string
    {
        $baseSlug = $this->slugify($baseText);
        $letter = $this->generateRandomLetter();
        $digits = $this->generateRandomDigits();
        $slug = $baseSlug . '-' . $letter . $digits;
        $attempt = 1;

        while ($checkExists($slug) && $attempt <= $maxAttempts) {
            $letter = $this->generateRandomLetter();
            $digits = $this->generateRandomDigits();
            $slug = $baseSlug . '-' . $letter . $digits;
            $attempt++;
        }

        return $slug;
    }

    /**
     * Génère un slug pour un provider basé sur son nom
     */
    public function generateProviderSlug(string $firstName, string $lastName, callable $checkExists): string
    {
        $baseText = $firstName . ' ' . $lastName;
        return $this->generateUniqueSlug($baseText, $checkExists);
    }
}
