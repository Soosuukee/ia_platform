// Provided Service Service: Handles API calls for provided services

const API_BASE = process.env.NEXT_PUBLIC_API_BASE || 'http://localhost:8080/api/v1';

interface ProvidedService {
  id: number;
  title: string;
  description: string;
  minPrice: number | null;
  maxPrice: number | null;
  duration: string;
  providerId: number;
}

export async function getProvidedService(id: number): Promise<ProvidedService> {
  const res = await fetch(`${API_BASE}/provided-services/${id}`, {
    method: 'GET',
    credentials: 'include',
  });
  
  if (!res.ok) {
    throw new Error('Failed to fetch service');
  }
  
  return res.json();
}

export async function getProviderServices(providerId: number): Promise<ProvidedService[]> {
  const res = await fetch(`${API_BASE}/providers/${providerId}/services`, {
    method: 'GET',
    credentials: 'include',
  });
  
  if (!res.ok) {
    throw new Error('Failed to fetch services');
  }
  
  return res.json();
}

export async function createProviderService(providerId: number, data: {
  title: string;
  description: string;
  minPrice?: number;
  maxPrice?: number;
  duration: string;
}): Promise<any> {
  const res = await fetch(`${API_BASE}/providers/${providerId}/services`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
    credentials: 'include',
  });
  
  if (!res.ok) {
    const error = await res.json();
    throw new Error(error.error || 'Failed to create service');
  }
  
  return res.json();
}

export async function updateProvidedService(id: number, data: Partial<{
  title: string;
  description: string;
  minPrice: number;
  maxPrice: number;
  duration: string;
}>): Promise<any> {
  const res = await fetch(`${API_BASE}/provided-services/${id}`, {
    method: 'PATCH',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
    credentials: 'include',
  });
  
  if (!res.ok) {
    const error = await res.json();
    throw new Error(error.error || 'Failed to update service');
  }
  
  return res.json();
}

export async function deleteProvidedService(id: number): Promise<any> {
  const res = await fetch(`${API_BASE}/provided-services/${id}`, {
    method: 'DELETE',
    credentials: 'include',
  });
  
  if (!res.ok) {
    const error = await res.json();
    throw new Error(error.error || 'Failed to delete service');
  }
  
  return res.json();
} 