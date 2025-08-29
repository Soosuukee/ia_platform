import { Provider } from '@/app/types/provider';
import { Job } from '@/app/types/job';
import { Language } from '@/app/types/language';

// URL de base de l'API
const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:3000/api';

/**
 * Récupère tous les providers depuis l'API
 */
export async function getAllProviders(): Promise<Provider[]> {
  try {
    const response = await fetch(`${API_BASE_URL}/providers`);
    if (!response.ok) {
      throw new Error(`Erreur HTTP: ${response.status}`);
    }
    return await response.json();
  } catch (error) {
    console.error('Erreur lors de la récupération des providers:', error);
    return [];
  }
}

/**
 * Récupère tous les providers avec leurs jobs et langues
 */
export async function getAllProvidersWithDetails() {
  try {
    const providers = await getAllProviders();
    
    const providersWithDetails = await Promise.all(
      providers.map(async (provider) => {
        const [jobResponse, languagesResponse] = await Promise.all([
          fetch(`/api/providers/id/${provider.providerId}/job`),
          fetch(`/api/providers/id/${provider.providerId}/languages`)
        ]);
        
        const job = jobResponse.ok ? await jobResponse.json() : undefined;
        const languages = languagesResponse.ok ? await languagesResponse.json() : [];
        
        return {
          ...provider,
          job,
          languages
        };
      })
    );
    
    return providersWithDetails;
  } catch (error) {
    console.error('Erreur lors de la récupération des providers avec détails:', error);
    return [];
  }
}

/**
 * Trouve un provider par son slug
 */
export async function getProviderBySlug(slug: string): Promise<Provider | undefined> {
  try {
    const response = await fetch(`/api/providers/${slug}`);
    if (!response.ok) {
      if (response.status === 404) return undefined;
      throw new Error(`Erreur HTTP: ${response.status}`);
    }
    return await response.json();
  } catch (error) {
    console.error('Erreur lors de la récupération du provider par slug:', error);
    return undefined;
  }
}

/**
 * Trouve un provider par son slug avec ses détails (job et langues)
 */
export async function getProviderBySlugWithDetails(slug: string) {
  try {
    const provider = await getProviderBySlug(slug);
    if (!provider) return undefined;
    
    const [jobResponse, languagesResponse] = await Promise.all([
      fetch(`/api/providers/id/${provider.providerId}/job`),
      fetch(`/api/providers/id/${provider.providerId}/languages`)
    ]);
    
    const job = jobResponse.ok ? await jobResponse.json() : undefined;
    const languages = languagesResponse.ok ? await languagesResponse.json() : [];
    
    return {
      ...provider,
      job,
      languages
    };
  } catch (error) {
    console.error('Erreur lors de la récupération du provider avec détails:', error);
    return undefined;
  }
}

/**
 * Trouve un provider par son ID
 */
export async function getProviderById(providerId: number): Promise<Provider | undefined> {
  try {
    const response = await fetch(`/api/providers/id/${providerId}`);
    if (!response.ok) {
      if (response.status === 404) return undefined;
      throw new Error(`Erreur HTTP: ${response.status}`);
    }
    return await response.json();
  } catch (error) {
    console.error('Erreur lors de la récupération du provider par ID:', error);
    return undefined;
  }
}

/**
 * Trouve un provider par son ID avec ses détails (job et langues)
 */
export async function getProviderByIdWithDetails(providerId: number) {
  try {
    const provider = await getProviderById(providerId);
    if (!provider) return undefined;
    
    const [jobResponse, languagesResponse] = await Promise.all([
      fetch(`/api/providers/id/${providerId}/job`),
      fetch(`/api/providers/id/${providerId}/languages`)
    ]);
    
    const job = jobResponse.ok ? await jobResponse.json() : undefined;
    const languages = languagesResponse.ok ? await languagesResponse.json() : [];
    
    return {
      ...provider,
      job,
      languages
    };
  } catch (error) {
    console.error('Erreur lors de la récupération du provider avec détails:', error);
    return undefined;
  }
}

/**
 * Génère les paramètres statiques pour les pages de providers
 */
export async function generateProviderStaticParams() {
  try {
    const providers = await getAllProviders();
    
    return providers.map(provider => ({
      slug: provider.slug
    }));
  } catch (error) {
    console.error('Erreur lors de la génération des paramètres statiques:', error);
    return [];
  }
}
