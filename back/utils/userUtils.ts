import users from '@/data/users.json';
import providers from '@/data/providers.json';

export interface User {
  user_id: number;
  provider_id?: number;
  email: string;
  password: string;
  role: 'provider' | 'client' | 'admin';
  avatar?: string;
  is_active: boolean;
  created_at: string;
  last_login: string;
}

export interface UserWithProvider extends User {
  provider?: {
    firstName: string;
    lastName: string;
    slug: string;
    avatar: string;
    role: string;
  };
}

export function findUserByEmail(email: string): User | null {
  const user = users.find(user => user.email === email);
  return user ? { ...user, role: user.role as 'provider' | 'client' | 'admin' } : null;
}

export function findUserById(userId: number): User | null {
  const user = users.find(user => user.user_id === userId);
  return user ? { ...user, role: user.role as 'provider' | 'client' | 'admin' } : null;
}

export function getUserWithProvider(userId: number): UserWithProvider | null {
  const user = findUserById(userId);
  
  if (!user) {
    return null;
  }

  // Si c'est un client, retourner directement avec son avatar
  if (user.role === 'client') {
    return {
      ...user,
      provider: {
        firstName: user.email.split('@')[0],
        lastName: '',
        slug: `client-${user.user_id}`,
        avatar: user.avatar || '/images/avatar.jpg',
        role: 'Client',
      }
    };
  }

  // Si c'est un prestataire, chercher les infos du provider
  if (!user.provider_id) {
    return user as UserWithProvider;
  }

  const provider = providers.find(p => p.provider_id === user.provider_id);
  
  return {
    ...user,
    provider: provider ? {
      firstName: provider.firstName,
      lastName: provider.lastName,
      slug: provider.slug,
      avatar: provider.avatar,
      role: provider.role,
    } : undefined
  };
}

export function validateUserCredentials(email: string, password: string): User | null {
  const user = findUserByEmail(email);
  
  if (!user || !user.is_active) {
    return null;
  }

  // Vérification simple du mot de passe
  if (user.password === password) {
    return user;
  }

  return null;
}

export function updateLastLogin(userId: number): void {
  // TODO: Implémenter la mise à jour de last_login
  console.log(`Last login updated for user ${userId}`);
}
