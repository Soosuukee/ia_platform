const API_BASE = process.env.NEXT_PUBLIC_API_BASE || 'http://localhost:8080/api/v1';

interface AuthData {
  email: string;
  password: string;
}

interface AuthResponse {
  id: number;
  firstName: string;
  lastName: string;
  message: string;
  error?: string;
}

export function getCurrentUserId(): number | null {
  if (typeof window === 'undefined') return null;
  const userId = localStorage.getItem('userId');
  return userId ? parseInt(userId, 10) : null;
}

export function getCurrentUserType(): 'provider' | 'client' | null {
  if (typeof window === 'undefined') return null;
  const userType = localStorage.getItem('userType');
  return userType as 'provider' | 'client' | null;
}

export function getCurrentUserName(): string | null {
  if (typeof window === 'undefined') return null;
  return localStorage.getItem('userName');
}

export function isAuthenticated(): boolean {
  if (typeof window === 'undefined') return false;
  return !!localStorage.getItem('userId');
}

export async function loginProvider(data: AuthData): Promise<AuthResponse> {
  try {
    const res = await fetch(`${API_BASE}/providers/login`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify(data),
    });

    const result = await res.json();

    if (!res.ok) {
      return { ...result, id: 0, firstName: '', lastName: '' };
    }

    if (result.id) {
      localStorage.setItem('userId', result.id.toString());
      localStorage.setItem('userType', 'provider');
      if (result.firstName && result.lastName) {
        localStorage.setItem('userName', `${result.firstName} ${result.lastName}`);
      }
    }

    return result;
  } catch (error) {
    console.error('Error during login:', error);
    return {
      id: 0,
      firstName: '',
      lastName: '',
      message: '',
      error: 'Failed to login',
    };
  }
}

export async function loginClient(data: AuthData): Promise<AuthResponse> {
  try {
    const res = await fetch(`${API_BASE}/clients/login`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify(data),
    });

    const result = await res.json();

    if (!res.ok) {
      return { ...result, id: 0, firstName: '', lastName: '' };
    }

    if (result.id) {
      localStorage.setItem('userId', result.id.toString());
      localStorage.setItem('userType', 'client');
      if (result.firstName && result.lastName) {
        localStorage.setItem('userName', `${result.firstName} ${result.lastName}`);
      }
    }

    return result;
  } catch (error) {
    console.error('Error during login:', error);
    return {
      id: 0,
      firstName: '',
      lastName: '',
      message: '',
      error: 'Failed to login',
    };
  }
}

export async function logout(): Promise<void> {
  const userType = getCurrentUserType();
  if (!userType) return;

  try {
    const endpoint = userType === 'client' ? 'clients/logout' : 'providers/logout';
    await fetch(`${API_BASE}/${endpoint}`, {
      method: 'POST',
      credentials: 'include',
    });
  } finally {
    localStorage.removeItem('userId');
    localStorage.removeItem('userType');
    localStorage.removeItem('userName');
  }
} 