<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Controller;

use Soosuuke\IaPlatform\Repository\LanguageRepository;
use Soosuuke\IaPlatform\Entity\Language;

class LanguageController
{
    private LanguageRepository $languageRepository;

    public function __construct()
    {
        $this->languageRepository = new LanguageRepository();
    }

    // GET /languages
    public function getAllLanguages(): array
    {
        $languages = $this->languageRepository->findAll();
        return array_map(fn(Language $l) => $l->toArray(), $languages);
    }

    // GET /languages/{id}
    public function getLanguageById(int $id): ?array
    {
        $lang = $this->languageRepository->findById($id);
        return $lang ? $lang->toArray() : null;
    }

    // POST /languages
    public function createLanguage(array $data): Language
    {
        $language = new Language($data['name']);
        $this->languageRepository->save($language);
        return $language;
    }

    // PUT /languages/{id}
    public function updateLanguage(int $id, array $data): ?Language
    {
        $language = $this->languageRepository->findById($id);
        if (!$language) {
            return null;
        }

        $language = new Language($data['name'] ?? $language->getName());
        $this->languageRepository->update($language);
        return $language;
    }

    // DELETE /languages/{id}
    public function deleteLanguage(int $id): bool
    {
        $language = $this->languageRepository->findById($id);
        if (!$language) {
            return false;
        }

        $this->languageRepository->delete($id);
        return true;
    }
}
