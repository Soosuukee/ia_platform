// Completed Work Service: Handles API calls for completed works

const API_BASE = process.env.NEXT_PUBLIC_API_BASE || 'http://localhost:8080/api/v1';

interface CompletedWork {
  id: number;
  company: string;
  title: string;
  description: string;
  startDate: string;
  endDate: string | null;
  providerId: number;
  media?: Array<{
    id: number;
    mediaType: string;
    mediaUrl: string;
  }>;
}

export async function getCompletedWorks(): Promise<CompletedWork[]> {
  const res = await fetch(`${API_BASE}/completed-works`, {
    method: 'GET',
    credentials: 'include',
  });
  
  if (!res.ok) {
    throw new Error('Failed to fetch completed works');
  }
  
  return res.json();
}

export async function createCompletedWork(data: {
  company: string;
  title: string;
  description: string;
  startDate: string;
  endDate?: string;
  media?: Array<{
    mediaType: string;
    mediaUrl: string;
  }>;
}): Promise<any> {
  const res = await fetch(`${API_BASE}/completed-works`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
    credentials: 'include',
  });
  
  if (!res.ok) {
    const error = await res.json();
    throw new Error(error.error || 'Failed to create completed work');
  }
  
  return res.json();
}

export async function updateCompletedWork(id: number, data: Partial<{
  company: string;
  title: string;
  description: string;
  startDate: string;
  endDate: string;
}>): Promise<any> {
  const res = await fetch(`${API_BASE}/completed-works/${id}`, {
    method: 'PATCH',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
    credentials: 'include',
  });
  
  if (!res.ok) {
    const error = await res.json();
    throw new Error(error.error || 'Failed to update completed work');
  }
  
  return res.json();
}

export async function deleteCompletedWork(id: number): Promise<any> {
  const res = await fetch(`${API_BASE}/completed-works/${id}`, {
    method: 'DELETE',
    credentials: 'include',
  });
  
  if (!res.ok) {
    const error = await res.json();
    throw new Error(error.error || 'Failed to delete completed work');
  }
  
  return res.json();
} 