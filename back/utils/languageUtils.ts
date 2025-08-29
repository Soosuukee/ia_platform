import { SpokenLanguage } from '@/app/types/spokenLanguage';
import languages from '@/data/languages.json';

/**
 * Transforme les données JSON vers le format SpokenLanguage
 */
function transformLanguageData(jsonLanguage: any): SpokenLanguage {
  return {
    languageId: jsonLanguage.languageId,
    name: jsonLanguage.name
  };
}

/**
 * Récupère toutes les langues
 */
export function getAllLanguages(): SpokenLanguage[] {
  return languages.map(transformLanguageData);
}

/**
 * Trouve une langue par son ID
 */
export function getLanguageById(languageId: number): SpokenLanguage | undefined {
  const allLanguages = getAllLanguages();
  return allLanguages.find(language => language.languageId === languageId);
}

/**
 * Trouve une langue par son nom
 */
export function getLanguageByName(name: string): SpokenLanguage | undefined {
  const allLanguages = getAllLanguages();
  return allLanguages.find(language => language.name.toLowerCase() === name.toLowerCase());
}

/**
 * Vérifie si un provider parle une langue spécifique
 */
export function hasProviderLanguage(providerLanguageIds: number[], languageId: number): boolean {
  return providerLanguageIds.includes(languageId);
}

/**
 * Récupère les détails des langues d'un provider
 */
export function getProviderLanguagesWithDetails(providerLanguageIds: number[]): SpokenLanguage[] {
  return providerLanguageIds
    .map(languageId => getLanguageById(languageId))
    .filter((language): language is SpokenLanguage => language !== undefined);
}
