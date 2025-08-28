// Provider Diploma Service: Handles API calls for provider diplomas

const API_BASE = process.env.NEXT_PUBLIC_API_BASE || 'http://localhost:8080/api/v1';

interface Diploma {
  id: number;
  title: string;
  institution: string;
  description: string | null;
  startDate: string | null;
  endDate: string | null;
  providerId: number;
}

export async function getProviderDiplomas(): Promise<Diploma[]> {
  const res = await fetch(`${API_BASE}/provider-diplomas`, {
    method: 'GET',
    credentials: 'include',
  });
  
  if (!res.ok) {
    throw new Error('Failed to fetch diplomas');
  }
  
  return res.json();
}

export async function createDiploma(data: {
  title: string;
  institution: string;
  description?: string;
  startDate?: string;
  endDate?: string;
}): Promise<any> {
  const res = await fetch(`${API_BASE}/provider-diplomas`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
    credentials: 'include',
  });
  
  if (!res.ok) {
    const error = await res.json();
    throw new Error(error.error || 'Failed to create diploma');
  }
  
  return res.json();
}

export async function updateDiploma(id: number, data: Partial<{
  title: string;
  institution: string;
  description: string;
  startDate: string;
  endDate: string;
}>): Promise<any> {
  const res = await fetch(`${API_BASE}/provider-diplomas/${id}`, {
    method: 'PATCH',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
    credentials: 'include',
  });
  
  if (!res.ok) {
    const error = await res.json();
    throw new Error(error.error || 'Failed to update diploma');
  }
  
  return res.json();
}

export async function deleteDiploma(id: number): Promise<any> {
  const res = await fetch(`${API_BASE}/provider-diplomas/${id}`, {
    method: 'DELETE',
    credentials: 'include',
  });
  
  if (!res.ok) {
    const error = await res.json();
    throw new Error(error.error || 'Failed to delete diploma');
  }
  
  return res.json();
} 