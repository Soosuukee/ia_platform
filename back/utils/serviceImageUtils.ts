import serviceImagesData from '@/data/serviceImages.json';
import { ServiceImage, ServiceImageFilters, ServiceImagesGroup } from '../types/serviceImage';

/**
 * Récupère toutes les images de service
 */
export const getAllServiceImages = (): ServiceImage[] => {
  return serviceImagesData.map(image => ({
    imageId: image.image_id,
    serviceId: image.service_id,
    title: image.title,
    url: image.url
  }));
};

/**
 * Récupère les images d'un service spécifique
 */
export const getServiceImages = (serviceId: number): ServiceImage[] => {
  return getAllServiceImages().filter(image => image.serviceId === serviceId);
};

/**
 * Récupère une image spécifique par son ID
 */
export const getServiceImageById = (imageId: number): ServiceImage | undefined => {
  return getAllServiceImages().find(image => image.imageId === imageId);
};

/**
 * Filtre les images selon les critères fournis
 */
export const filterServiceImages = (filters: ServiceImageFilters): ServiceImage[] => {
  let images = getAllServiceImages();

  if (filters.serviceId !== undefined) {
    images = images.filter(image => image.serviceId === filters.serviceId);
  }

  if (filters.title) {
    images = images.filter(image => 
      image.title.toLowerCase().includes(filters.title!.toLowerCase())
    );
  }

  return images;
};

/**
 * Groupe les images par service
 */
export const groupServiceImagesByService = (): ServiceImagesGroup[] => {
  const images = getAllServiceImages();
  const grouped: { [key: number]: ServiceImage[] } = {};

  images.forEach(image => {
    if (!grouped[image.serviceId]) {
      grouped[image.serviceId] = [];
    }
    grouped[image.serviceId].push(image);
  });

  return Object.entries(grouped).map(([serviceId, images]) => ({
    serviceId: parseInt(serviceId),
    images
  }));
};

/**
 * Récupère la première image d'un service (généralement l'image de couverture)
 */
export const getServiceCoverImage = (serviceId: number): ServiceImage | undefined => {
  const serviceImages = getServiceImages(serviceId);
  return serviceImages.find(image => image.title.includes('cover')) || serviceImages[0];
};

/**
 * Récupère les images de couverture pour tous les services
 */
export const getAllServiceCoverImages = (): ServiceImage[] => {
  const allImages = getAllServiceImages();
  const coverImages: ServiceImage[] = [];
  const processedServices = new Set<number>();

  allImages.forEach(image => {
    if (image.title.includes('cover') && !processedServices.has(image.serviceId)) {
      coverImages.push(image);
      processedServices.add(image.serviceId);
    }
  });

  return coverImages;
};
