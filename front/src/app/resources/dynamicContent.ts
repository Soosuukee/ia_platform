import { getProvider, getProviderProfile } from '@/services/providerService';
import { getProviderServices } from '@/services/providedServiceService';
import { getCompletedWorks } from '@/services/completedWorkService';
import { getProviderSkills } from '@/services/providerSkillService';
import { getProviderDiplomas } from '@/services/providerDiplomaService';
import { getProviderReviews } from '@/services/reviewService';
import { Provider, Service, Skill, Diploma, Review, ServicesData } from './types';

// Default provider ID - you might want to make this configurable
const DEFAULT_PROVIDER_ID = 1;

export async function getPersonData(providerId = DEFAULT_PROVIDER_ID) {
  try {
    const provider = await getProvider(providerId) as Provider;
    return {
      firstName: provider.firstName,
      lastName: provider.lastName,
      name: `${provider.firstName} ${provider.lastName}`,
      role: provider.title,
      avatar: provider.profilePicture || "/images/avatar.jpg",
      email: provider.email,
      location: provider.country,
      languages: [], // Add if you have this data in your API
    };
  } catch (error) {
    console.error('Error fetching provider data:', error);
    return null;
  }
}

export async function getServicesData(providerId = DEFAULT_PROVIDER_ID): Promise<ServicesData | null> {
  try {
    const services = await getProviderServices(providerId) as Service[];
    return {
      path: "/services",
      label: "Services",
      title: "Our Services",
      description: "Browse our available services",
      services: services,
    };
  } catch (error) {
    console.error('Error fetching services:', error);
    return null;
  }
}

export async function getWorkData(providerId = DEFAULT_PROVIDER_ID) {
  try {
    const works = await getCompletedWorks();
    return {
      path: "/work",
      label: "Work",
      title: "Our Projects",
      description: "View our completed works",
      projects: works,
    };
  } catch (error) {
    console.error('Error fetching works:', error);
    return null;
  }
}

export async function getAboutData(providerId = DEFAULT_PROVIDER_ID) {
  try {
    const [provider, skills, diplomas] = await Promise.all([
      getProviderProfile(providerId) as Promise<Provider>,
      getProviderSkills(providerId) as Promise<Skill[]>,
      getProviderDiplomas() as Promise<Diploma[]>
    ]);

    return {
      path: "/about",
      label: "About",
      title: `About – ${provider.firstName} ${provider.lastName}`,
      description: `Meet ${provider.firstName} ${provider.lastName}, ${provider.title} from ${provider.country}`,
      tableOfContent: {
        display: true,
        subItems: false,
      },
      avatar: {
        display: true,
      },
      intro: {
        display: true,
        title: "Introduction",
        description: provider.presentation,
      },
      technical: {
        display: true,
        title: "Skills",
        skills: skills.map((skill: Skill) => ({
          title: skill.name,
          description: skill.description,
        })),
      },
      education: {
        display: true,
        title: "Education",
        diplomas: diplomas.map((diploma: Diploma) => ({
          name: diploma.title,
          institution: diploma.institution,
          description: diploma.description,
          startDate: diploma.startDate,
          endDate: diploma.endDate,
        })),
      },
    };
  } catch (error) {
    console.error('Error fetching about data:', error);
    return null;
  }
}

export async function getReviewsData(providerId = DEFAULT_PROVIDER_ID) {
  try {
    const reviews = await getProviderReviews(providerId) as Review[];
    return {
      reviews: reviews.map((review: Review) => ({
        rating: review.rating,
        content: review.content,
        author: review.clientName,
        date: review.createdAt,
      })),
    };
  } catch (error) {
    console.error('Error fetching reviews:', error);
    return null;
  }
} 