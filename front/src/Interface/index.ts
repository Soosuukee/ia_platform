// =============================================================================
// INTERFACES CENTRALISÉES - Entités Backend
// =============================================================================
// Ce fichier contient toutes les interfaces TypeScript correspondant 
// aux entités PHP du backend pour assurer la cohérence des types.

// =============================================================================
// ENTITÉS PRINCIPALES
// =============================================================================

export interface Client {
  id: number;
  firstName: string;
  lastName: string;
  email: string;
  country: string;
  role: 'client';
  createdAt: string; // Format ISO string depuis DateTimeImmutable
}

export interface Provider {
  id: number;
  firstName: string;
  lastName: string;
  email: string;
  country: string;
  profilePicture: string | null;
  role: 'provider';
  createdAt: string; // Format ISO string depuis DateTimeImmutable
  title: string;
  presentation: string;
  socialLinks: string[];
  // Relations (optionnelles selon le contexte)
  availabilitySlots?: AvailabilitySlot[];
  skills?: Skill[];
  services?: ProvidedService[];
  diplomas?: ProviderDiploma[];
  completedWorks?: CompletedWork[];
  reviews?: Review[];
  requests?: Request[];
}

export interface Skill {
  id: number;
  name: string;
}

export interface ProviderSkill {
  providerId: number;
  skillId: number;
}

// =============================================================================
// SERVICES ET RÉSERVATIONS
// =============================================================================

export interface ProvidedService {
  id: number;
  title: string;
  description: string;
  minPrice: number | null;
  maxPrice: number | null;
  duration: string;
  providerId: number;
}

export interface AvailabilitySlot {
  id: number;
  providerId: number;
  startTime: string; // Format ISO string depuis DateTimeImmutable
  endTime: string; // Format ISO string depuis DateTimeImmutable
  isBooked: boolean;
  // Alias pour compatibilité
  start?: string;
  end?: string;
}

export interface Booking {
  id: number;
  status: 'pending' | 'accepted' | 'declined';
  clientId: number;
  slotId: number;
  createdAt: string; // Format ISO string depuis DateTimeImmutable
}

// =============================================================================
// COMMUNICATION ET ÉVALUATIONS
// =============================================================================

export interface Request {
  id: number; // requestId dans l'entité PHP
  clientId: number;
  providerId: number;
  title: string;
  description: string;
  createdAt: string; // Format ISO string depuis DateTimeImmutable
  status: 'pending' | 'accepted' | 'declined' | 'completed';
}

export interface Review {
  id: number;
  clientId: number;
  providerId: number;
  content: string;
  rating: number; // 1-5
  createdAt: string; // Format ISO string depuis DateTimeImmutable
}

export interface Notification {
  id: number;
  recipientId: number;
  recipientType: 'client' | 'provider';
  message: string;
  isRead: boolean;
  createdAt: string; // Format ISO string depuis DateTimeImmutable
}

// =============================================================================
// ÉDUCATION ET PORTFOLIO
// =============================================================================

export interface ProviderDiploma {
  id: number;
  title: string;
  institution: string;
  description: string;
  startDate: string | null; // Format YYYY-MM-DD depuis DateTime
  endDate: string | null; // Format YYYY-MM-DD depuis DateTime
  providerId: number;
}

export interface CompletedWork {
  id: number;
  providerId: number;
  company: string;
  title: string;
  description: string;
  startDate: string; // Format YYYY-MM-DD depuis DateTimeImmutable
  endDate: string | null; // Format YYYY-MM-DD depuis DateTimeImmutable
  // Relations
  media?: CompletedWorkMedia[];
}

export interface CompletedWorkMedia {
  id: number;
  workId: number; // Correspond à CompletedWork.id
  mediaType: string; // 'image', 'video', 'document', etc.
  mediaUrl: string;
}

// =============================================================================
// TYPES DE FORMULAIRES ET REQUÊTES
// =============================================================================

// Formulaires de création (sans ID)
export interface CreateClientData {
  firstName: string;
  lastName: string;
  email: string;
  password: string;
  country: string;
}

export interface CreateProviderData {
  firstName: string;
  lastName: string;
  email: string;
  password: string;
  title: string;
  presentation: string;
  country: string;
  profilePicture?: string;
  socialLinks?: string[];
}

export interface CreateServiceData {
  title: string;
  description: string;
  minPrice?: number;
  maxPrice?: number;
  duration: string;
}

export interface CreateSlotData {
  startTime: string;
  endTime: string;
}

export interface CreateDiplomaData {
  title: string;
  institution: string;
  description?: string;
  startDate?: string;
  endDate?: string;
}

export interface CreateCompletedWorkData {
  company: string;
  title: string;
  description: string;
  startDate: string;
  endDate?: string;
}

export interface CreateRequestData {
  providerId: number;
  title: string;
  description: string;
}

export interface CreateReviewData {
  providerId: number;
  content: string;
  rating: number;
}

export interface CreateBookingData {
  status: string;
  slotId: number;
}

// Formulaires de mise à jour (ID optionnel)
export interface UpdateProviderData {
  title?: string;
  presentation?: string;
  country?: string;
  profilePicture?: string;
  socialLinks?: string[];
  firstName?: string;
  lastName?: string;
  email?: string;
  password?: string;
}

export interface UpdateServiceData {
  title?: string;
  description?: string;
  minPrice?: number;
  maxPrice?: number;
  duration?: string;
}

export interface UpdateSlotData {
  startTime?: string;
  endTime?: string;
  isBooked?: boolean;
}

export interface UpdateDiplomaData {
  title?: string;
  institution?: string;
  description?: string;
  startDate?: string;
  endDate?: string;
}

export interface UpdateCompletedWorkData {
  company?: string;
  title?: string;
  description?: string;
  startDate?: string;
  endDate?: string;
}

export interface UpdateRequestData {
  title?: string;
  description?: string;
  status?: 'pending' | 'accepted' | 'declined' | 'completed';
}

// =============================================================================
// TYPES DE RÉPONSES API
// =============================================================================

export interface ApiResponse<T = any> {
  message?: string;
  error?: string;
  data?: T;
}

export interface LoginResponse {
  message: string;
  id: number;
  firstName: string;
  lastName: string;
}

export interface DashboardData {
  message: string;
  provider: Provider;
}

export interface PaginatedResponse<T> {
  data: T[];
  total: number;
  page: number;
  limit: number;
}

// =============================================================================
// UTILITAIRES ET HELPERS
// =============================================================================

export type UserRole = 'client' | 'provider';
export type BookingStatus = 'pending' | 'accepted' | 'declined';
export type RequestStatus = 'pending' | 'accepted' | 'declined' | 'completed';
export type MediaType = 'image' | 'video' | 'document' | 'other';

// Union types pour les entités principales
export type User = Client | Provider;
export type AccountHolder = Client | Provider;

// Types utilitaires
export type EntityId = number;
export type DateString = string; // Format ISO ou YYYY-MM-DD
export type UrlString = string;

// =============================================================================
// CONSTANTES UTILES
// =============================================================================

export const USER_ROLES = {
  CLIENT: 'client' as const,
  PROVIDER: 'provider' as const
};

export const BOOKING_STATUSES = {
  PENDING: 'pending' as const,
  ACCEPTED: 'accepted' as const,
  DECLINED: 'declined' as const
};

export const REQUEST_STATUSES = {
  PENDING: 'pending' as const,
  ACCEPTED: 'accepted' as const,
  DECLINED: 'declined' as const,
  COMPLETED: 'completed' as const
};

export const MEDIA_TYPES = {
  IMAGE: 'image' as const,
  VIDEO: 'video' as const,
  DOCUMENT: 'document' as const,
  OTHER: 'other' as const
};

// =============================================================================
// GUARDS TYPE ET VALIDATIONS
// =============================================================================

export const isClient = (user: User): user is Client => {
  return user.role === 'client';
};

export const isProvider = (user: User): user is Provider => {
  return user.role === 'provider';
};

export const isValidEmail = (email: string): boolean => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
};

export const isValidRating = (rating: number): boolean => {
  return rating >= 1 && rating <= 5 && Number.isInteger(rating);
};

export const isValidBookingStatus = (status: string): status is BookingStatus => {
  return Object.values(BOOKING_STATUSES).includes(status as BookingStatus);
};

export const isValidRequestStatus = (status: string): status is RequestStatus => {
  return Object.values(REQUEST_STATUSES).includes(status as RequestStatus);
};

// =============================================================================
// EXPORT PAR DÉFAUT - CONSTANTES ET FONCTIONS SEULEMENT
// =============================================================================

export default {
  // Constantes
  USER_ROLES,
  BOOKING_STATUSES,
  REQUEST_STATUSES,
  MEDIA_TYPES,
  
  // Guards et validations
  isClient,
  isProvider,
  isValidEmail,
  isValidRating,
  isValidBookingStatus,
  isValidRequestStatus
}; 