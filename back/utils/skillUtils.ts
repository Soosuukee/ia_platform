import { Skill } from '@/app/types/skill';
import skills from '@/data/skills.json';

/**
 * Transforme les données JSON vers le format Skill
 */
function transformSkillData(jsonSkill: any): Skill {
  return {
    skillId: jsonSkill.skill_id,
    name: jsonSkill.name,
    category: undefined, // À ajouter plus tard si nécessaire
    description: undefined // À ajouter plus tard si nécessaire
  };
}

/**
 * Récupère toutes les compétences
 */
export function getAllSkills(): Skill[] {
  return skills.map(transformSkillData);
}

/**
 * Trouve une compétence par son ID
 */
export function getSkillById(skillId: number): Skill | undefined {
  const allSkills = getAllSkills();
  return allSkills.find(skill => skill.skillId === skillId);
}

/**
 * Trouve une compétence par son nom
 */
export function getSkillByName(name: string): Skill | undefined {
  const allSkills = getAllSkills();
  return allSkills.find(skill => skill.name.toLowerCase() === name.toLowerCase());
}

/**
 * Recherche des compétences par mot-clé
 */
export function searchSkills(keyword: string): Skill[] {
  const allSkills = getAllSkills();
  return allSkills.filter(skill => 
    skill.name.toLowerCase().includes(keyword.toLowerCase())
  );
}

/**
 * Récupère les compétences par catégorie
 */
export function getSkillsByCategory(category: string): Skill[] {
  const allSkills = getAllSkills();
  return allSkills.filter(skill => skill.category === category);
}

/**
 * Récupère toutes les catégories de compétences
 */
export function getAllSkillCategories(): string[] {
  const allSkills = getAllSkills();
  const categories = allSkills
    .map(skill => skill.category)
    .filter((category): category is string => category !== undefined);
  return [...new Set(categories)]; // Supprime les doublons
}

/**
 * Vérifie si un provider a une compétence spécifique
 */
export function hasProviderSkill(providerSkillIds: number[], skillId: number): boolean {
  return providerSkillIds.includes(skillId);
}

/**
 * Récupère les détails des compétences d'un provider
 */
export function getProviderSkillsWithDetails(providerSkillIds: number[]): Skill[] {
  return providerSkillIds
    .map(skillId => getSkillById(skillId))
    .filter((skill): skill is Skill => skill !== undefined);
}

/**
 * Filtre les compétences d'un provider par catégorie
 */
export function getProviderSkillsByCategory(providerSkillIds: number[], category: string): Skill[] {
  const providerSkills = getProviderSkillsWithDetails(providerSkillIds);
  return providerSkills.filter(skill => skill.category === category);
}
