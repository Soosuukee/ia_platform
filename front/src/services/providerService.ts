// Provider Service: Handles API calls for provider-related actions

const API_BASE = process.env.NEXT_PUBLIC_API_BASE || 'http://localhost:8080/api/v1';

interface Provider {
  id: number;
  firstName: string;
  lastName: string;
  title: string;
  presentation: string;
  country: string;
  profilePicture: string | null;
  socialLinks: string[];
  skills: Array<{
    id: number;
    name: string;
  }>;
  completedWorks: Array<{
    id: number;
    company: string;
    title: string;
    description: string;
    startDate: string;
    endDate: string | null;
  }>;
  reviews: Array<{
    id: number;
    rating: number;
    content: string;
    createdAt: string;
  }>;
  diplomas: Array<{
    id: number;
    title: string;
    institution: string;
    description: string | null;
    startDate: string | null;
    endDate: string | null;
  }>;
  services: Array<{
    id: number;
    title: string;
    description: string;
    minPrice: number | null;
    maxPrice: number | null;
    duration: string;
  }>;
}

interface ProviderResponse {
  provider: Provider;
}

export async function getProviderPublicProfile(id: number): Promise<Provider> {
  const url = `${API_BASE}/providers/${id}/profile`;
  console.log('Fetching provider from URL:', url);
  
  try {
    const res = await fetch(url, {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      }
    });

    console.log('Response status:', res.status);
    console.log('Response headers:', Object.fromEntries(res.headers.entries()));

    if (!res.ok) {
      const errorData = await res.text();
      console.error('Error response:', errorData);
      throw new Error(`Failed to fetch provider profile: ${res.status} ${errorData}`);
    }

    const data = await res.json() as ProviderResponse;
    console.log('Provider data:', data);
    return data.provider;
  } catch (error) {
    console.error('Fetch error:', error);
    throw error;
  }
}

export async function registerProvider(data: any) {
  const res = await fetch(`${API_BASE}/providers/register`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
    credentials: 'include',
  });
  return res.json();
}

export async function loginProvider(data: any) {
  const res = await fetch(`${API_BASE}/providers/login`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
    credentials: 'include',
  });
  return res.json();
}

export async function logoutProvider() {
  const res = await fetch(`${API_BASE}/providers/logout`, {
    method: 'POST',
    credentials: 'include',
  });
  return res.json();
}

export async function getProvider(id: number) {
  const res = await fetch(`${API_BASE}/providers/${id}`, {
    method: 'GET',
    credentials: 'include',
  });
  return res.json();
}

export async function deleteProvider(id: number) {
  const res = await fetch(`${API_BASE}/providers/${id}`, {
    method: 'DELETE',
    credentials: 'include',
  });
  return res.json();
}

export async function getProviderDashboard(id: number) {
  const res = await fetch(`${API_BASE}/providers/${id}/dashboard`, {
    method: 'GET',
    credentials: 'include',
  });
  
  if (!res.ok) {
    const error = await res.json();
    throw new Error(error.error || 'Failed to fetch dashboard data');
  }
  
  return res.json();
}

export async function updateProviderDashboard(id: number, data: any) {
  const res = await fetch(`${API_BASE}/providers/${id}/dashboard`, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
    credentials: 'include',
  });
  
  if (!res.ok) {
    const error = await res.json();
    throw new Error(error.error || 'Failed to update profile');
  }
  
  return res.json();
}

export async function getProviderProfile(id: number) {
  const res = await fetch(`${API_BASE}/providers/${id}/profile`, {
    method: 'GET',
    credentials: 'include',
  });
  return res.json();
}

export async function getAllProviders(): Promise<Provider[]> {
  const res = await fetch(`${API_BASE}/providers`, {
    method: 'GET',
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    }
  });

  if (!res.ok) {
    throw new Error('Failed to fetch providers');
  }

  const data = await res.json();
  return data.providers;
} 