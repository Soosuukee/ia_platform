import { Experience } from '@/app/types/experience';
import experiences from '@/data/providerExperiences.json';

/**
 * Transforme les données JSON vers le format Experience
 */
function transformExperienceData(jsonExperience: any): Experience {
  return {
    experienceId: jsonExperience.experienceId,
    jobTitle: jsonExperience.jobTitle,
    companyName: jsonExperience.companyName,
    description: jsonExperience.description,
    task1: jsonExperience.task1,
    task2: jsonExperience.task2,
    startDate: jsonExperience.startDate,
    endDate: jsonExperience.endDate,
    experienceImage: jsonExperience.experienceImage
  };
}

/**
 * Récupère toutes les expériences
 */
export function getAllExperiences(): Experience[] {
  return experiences.map(transformExperienceData);
}

/**
 * Trouve une expérience par son ID
 */
export function getExperienceById(experienceId: number): Experience | undefined {
  const allExperiences = getAllExperiences();
  return allExperiences.find(experience => experience.experienceId === experienceId);
}

/**
 * Calcule la durée d'une expérience en mois
 */
export function calculateExperienceDuration(startDate: Date, endDate?: Date): number {
  const end = endDate || new Date();
  const diffTime = Math.abs(end.getTime() - startDate.getTime());
  const diffMonths = Math.ceil(diffTime / (1000 * 60 * 60 * 24 * 30.44)); // Moyenne de jours par mois
  return diffMonths;
}

/**
 * Formate la durée d'une expérience en texte lisible
 */
export function formatExperienceDuration(startDate: Date, endDate?: Date): string {
  const months = calculateExperienceDuration(startDate, endDate);
  
  if (months < 12) {
    return `${months} mois`;
  }
  
  const years = Math.floor(months / 12);
  const remainingMonths = months % 12;
  
  if (remainingMonths === 0) {
    return `${years} an${years > 1 ? 's' : ''}`;
  }
  
  return `${years} an${years > 1 ? 's' : ''} et ${remainingMonths} mois`;
}
