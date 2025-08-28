// Availability Slot Service: Handles API calls for provider availability slots

const API_BASE = process.env.NEXT_PUBLIC_API_BASE || 'http://localhost:8080/api/v1';

interface AvailabilitySlot {
  id: number;
  startTime: string;
  endTime: string;
  isBooked: boolean;
}

export async function getProviderSlots(providerId: number): Promise<AvailabilitySlot[]> {
  const res = await fetch(`${API_BASE}/providers/${providerId}/slots`, {
    method: 'GET',
    credentials: 'include',
  });
  
  if (!res.ok) {
    throw new Error('Failed to fetch availability slots');
  }
  
  return res.json();
}

export async function createSlot(providerId: number, data: {
  startTime: string;
  endTime: string;
}): Promise<any> {
  const res = await fetch(`${API_BASE}/providers/${providerId}/slots`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
    credentials: 'include',
  });
  
  if (!res.ok) {
    const error = await res.json();
    throw new Error(error.error || 'Failed to create slot');
  }
  
  return res.json();
}

export async function updateSlot(slotId: number, data: {
  startTime: string;
  endTime: string;
}): Promise<any> {
  const res = await fetch(`${API_BASE}/slots/${slotId}`, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
    credentials: 'include',
  });
  
  if (!res.ok) {
    const error = await res.json();
    throw new Error(error.error || 'Failed to update slot');
  }
  
  return res.json();
}

export async function deleteSlot(slotId: number): Promise<any> {
  const res = await fetch(`${API_BASE}/slots/${slotId}`, {
    method: 'DELETE',
    credentials: 'include',
  });
  
  if (!res.ok) {
    const error = await res.json();
    throw new Error(error.error || 'Failed to delete slot');
  }
  
  return res.json();
} 