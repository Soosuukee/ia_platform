import fs from 'fs';
import path from 'path';
import matter from 'gray-matter';

export interface ServiceContent {
  title: string;
  provider: string;
  service_id: string;
  summary: string;
  publishedAt: string;
  min_price: number | null;
  max_price: number | null;
  images?: string[];
  content: string;
}

const servicesDirectory = path.join(process.cwd(), 'src/app/providers/services');

export function getServiceContent(providerSlug: string, serviceSlug: string): ServiceContent | null {
  try {
    // Chercher le fichier MDX correspondant au service
    const files = fs.readdirSync(servicesDirectory);
    const serviceFile = files.find(file => {
      if (!file.endsWith('.mdx')) return false;
      
      // Vérifier si le fichier correspond au provider et au service
      const filePath = path.join(servicesDirectory, file);
      const fileContent = fs.readFileSync(filePath, 'utf8');
      const { data } = matter(fileContent);
      
      return data.provider === providerSlug && 
             (data.service_id === serviceSlug || file.replace('.mdx', '') === serviceSlug);
    });

    if (!serviceFile) {
      return null;
    }

    const filePath = path.join(servicesDirectory, serviceFile);
    const fileContent = fs.readFileSync(filePath, 'utf8');
    const { data, content } = matter(fileContent);

    return {
      title: data.title || '',
      provider: data.provider || '',
      service_id: data.service_id || '',
      summary: data.summary || '',
      publishedAt: data.publishedAt || '',
      min_price: data.min_price || null,
      max_price: data.max_price || null,
      images: data.images || [],
      content: content,
    };
  } catch (error) {
    console.error('Erreur lors du chargement du contenu du service:', error);
    return null;
  }
}

export function getAllServiceContents(): ServiceContent[] {
  try {
    const files = fs.readdirSync(servicesDirectory);
    const services: ServiceContent[] = [];

    for (const file of files) {
      if (!file.endsWith('.mdx')) continue;

      const filePath = path.join(servicesDirectory, file);
      const fileContent = fs.readFileSync(filePath, 'utf8');
      const { data, content } = matter(fileContent);

      services.push({
        title: data.title || '',
        provider: data.provider || '',
        service_id: data.service_id || '',
        summary: data.summary || '',
        publishedAt: data.publishedAt || '',
        min_price: data.min_price || null,
        max_price: data.max_price || null,
        images: data.images || [],
        content: content,
      });
    }

    return services;
  } catch (error) {
    console.error('Erreur lors du chargement des contenus de services:', error);
    return [];
  }
}

export function getProviderServices(providerSlug: string): ServiceContent[] {
  const allServices = getAllServiceContents();
  return allServices.filter(service => service.provider === providerSlug);
}
