// Request Service: Handles API calls for requests

const API_BASE = process.env.NEXT_PUBLIC_API_BASE || 'http://localhost:8080/api/v1';

export async function createRequest(data: any) {
  const res = await fetch(`${API_BASE}/requests`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
    credentials: 'include',
  });
  return res.json();
}

export async function getRequestsByProvider(providerId: number) {
  const res = await fetch(`${API_BASE}/requests/provider/${providerId}`, {
    method: 'GET',
    credentials: 'include',
  });
  return res.json();
}

export async function getRequestsByClient(clientId: number) {
  const res = await fetch(`${API_BASE}/requests/client/${clientId}`, {
    method: 'GET',
    credentials: 'include',
  });
  return res.json();
}

export async function updateRequestStatus(id: number, data: any) {
  const res = await fetch(`${API_BASE}/requests/${id}`, {
    method: 'PATCH',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
    credentials: 'include',
  });
  return res.json();
}

export async function deleteRequest(id: number) {
  const res = await fetch(`${API_BASE}/requests/${id}`, {
    method: 'DELETE',
    credentials: 'include',
  });
  return res.json();
} 