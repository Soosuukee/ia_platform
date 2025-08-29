import { Education } from '@/app/types/education';
import education from '@/data/providerEducation.json';

/**
 * Transforme les données JSON vers le format Education
 */
function transformEducationData(jsonEducation: any): Education {
  return {
    diplomaId: jsonEducation.diploma_id,
    diplomaTitle: jsonEducation.diploma_title,
    institutionName: jsonEducation.institution_name,
    description: jsonEducation.description,
    startDate: jsonEducation.start_date,
    endDate: jsonEducation.end_date,
    diplomaImageUrl: jsonEducation.diploma_image_url
  };
}

/**
 * Récupère toutes les formations
 */
export function getAllEducation(): Education[] {
  return education.map(transformEducationData);
}

/**
 * Trouve une formation par son ID
 */
export function getEducationById(educationId: number): Education | undefined {
  const allEducation = getAllEducation();
  return allEducation.find(edu => edu.diplomaId === educationId);
}

/**
 * Calcule la durée d'une formation en mois
 */
export function calculateEducationDuration(startDate: Date, endDate?: Date): number {
  const end = endDate || new Date();
  const diffTime = Math.abs(end.getTime() - startDate.getTime());
  const diffMonths = Math.ceil(diffTime / (1000 * 60 * 60 * 24 * 30.44)); // Moyenne de jours par mois
  return diffMonths;
}

/**
 * Formate la durée d'une formation en texte lisible
 */
export function formatEducationDuration(startDate: Date, endDate?: Date): string {
  const months = calculateEducationDuration(startDate, endDate);
  
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

/**
 * Vérifie si une formation est en cours
 */
export function isEducationInProgress(endDate?: Date): boolean {
  return !endDate || endDate > new Date();
}
