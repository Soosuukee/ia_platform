export interface Service {
  id: number;
  title: string;
  description: string;
  duration: string;
  minPrice?: number;
  maxPrice?: number;
  providerId: number;
}

export interface Provider {
  id: number;
  firstName: string;
  lastName: string;
  email: string;
  title: string;
  presentation: string;
  country: string;
  profilePicture?: string;
}

export interface Skill {
  id: number;
  name: string;
  description: string;
}

export interface Diploma {
  id: number;
  title: string;
  institution: string;
  description: string;
  startDate: string;
  endDate?: string;
}

export interface Review {
  id: number;
  rating: number;
  content: string;
  clientName: string;
  createdAt: string;
}

export interface ServicesData {
  path: string;
  label: string;
  title: string;
  description: string;
  services: Service[];
} 