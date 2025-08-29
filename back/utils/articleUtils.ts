import { Article } from '@/app/types/article';

// URL de base de l'API
const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:3000/api';

/**
 * Récupère tous les articles depuis l'API
 */
export async function getAllArticles(): Promise<Article[]> {
  try {
    const response = await fetch('/api/articles');
    if (!response.ok) {
      throw new Error(`Erreur HTTP: ${response.status}`);
    }
    return await response.json();
  } catch (error) {
    console.error('Erreur lors de la récupération des articles:', error);
    return [];
  }
}

/**
 * Récupère les articles d'un provider spécifique depuis l'API
 */
export async function getArticlesByProvider(providerId: number): Promise<Article[]> {
  try {
    const response = await fetch(`/api/providers/${providerId}/articles`);
    if (!response.ok) {
      throw new Error(`Erreur HTTP: ${response.status}`);
    }
    return await response.json();
  } catch (error) {
    console.error('Erreur lors de la récupération des articles du provider:', error);
    return [];
  }
}

/**
 * Récupère un article par son slug depuis l'API
 */
export async function getArticleBySlug(slug: string): Promise<Article | undefined> {
  try {
    // Utiliser une URL absolue côté serveur
    const baseUrl = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:3000';
    const response = await fetch(`${baseUrl}/api/articles/${slug}`);
    if (!response.ok) {
      if (response.status === 404) return undefined;
      throw new Error(`Erreur HTTP: ${response.status}`);
    }
    return await response.json();
  } catch (error) {
    console.error('Erreur lors de la récupération de l\'article par slug:', error);
    return undefined;
  }
}

/**
 * Récupère un article par son slug et son provider depuis l'API
 */
export async function getArticleBySlugAndProvider(articleSlug: string, providerId: number): Promise<Article | undefined> {
  try {
    const response = await fetch(`/api/providers/${providerId}/articles/${articleSlug}`);
    if (!response.ok) {
      if (response.status === 404) return undefined;
      throw new Error(`Erreur HTTP: ${response.status}`);
    }
    return await response.json();
  } catch (error) {
    console.error('Erreur lors de la récupération de l\'article par slug et provider:', error);
    return undefined;
  }
}

/**
 * Récupère les articles triés par date de publication (plus récents en premier)
 */
export async function getSortedArticles(): Promise<Article[]> {
  try {
    const articles = await getAllArticles();
    return articles.sort((a, b) => {
      return new Date(b.publishedAt).getTime() - new Date(a.publishedAt).getTime();
    });
  } catch (error) {
    console.error('Erreur lors du tri des articles:', error);
    return [];
  }
}

/**
 * Récupère les articles publiés uniquement
 */
export async function getPublishedArticles(): Promise<Article[]> {
  try {
    const articles = await getAllArticles();
    return articles.filter(article => article.isPublished);
  } catch (error) {
    console.error('Erreur lors de la récupération des articles publiés:', error);
    return [];
  }
}

/**
 * Récupère les articles par tag
 */
export async function getArticlesByTag(tag: string): Promise<Article[]> {
  try {
    const articles = await getAllArticles();
    return articles.filter(article => article.tag === tag);
  } catch (error) {
    console.error('Erreur lors de la récupération des articles par tag:', error);
    return [];
  }
}

/**
 * Récupère les articles par langue
 */
export async function getArticlesByLanguage(language: string): Promise<Article[]> {
  try {
    const articles = await getAllArticles();
    return articles.filter(article => article.language === language);
  } catch (error) {
    console.error('Erreur lors de la récupération des articles par langue:', error);
    return [];
  }
}

/**
 * Génère les paramètres statiques pour les pages d'articles
 */
export async function generateArticleStaticParams() {
  try {
    const articles = await getAllArticles();
    
    return articles.map(article => ({
      slug: article.slug
    }));
  } catch (error) {
    console.error('Erreur lors de la génération des paramètres statiques des articles:', error);
    return [];
  }
}

/**
 * Génère les paramètres statiques pour les pages d'articles par provider
 */
export async function generateArticleStaticParamsByProvider(providerId: number) {
  try {
    const articles = await getArticlesByProvider(providerId);
    
    return articles.map(article => ({
      articleSlug: article.slug
    }));
  } catch (error) {
    console.error('Erreur lors de la génération des paramètres statiques des articles par provider:', error);
    return [];
  }
}
