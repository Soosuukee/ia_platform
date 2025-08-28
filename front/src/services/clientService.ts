// Client Service: Handles API calls for client-related actions

const API_BASE = process.env.NEXT_PUBLIC_API_BASE || 'http://localhost:8080/api/v1';

export async function registerClient(data: any) {
  const res = await fetch(`${API_BASE}/clients/register`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
    credentials: 'include',
  });
  return res.json();
}

export async function loginClient(data: any) {
  const res = await fetch(`${API_BASE}/clients/login`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
    credentials: 'include',
  });
  return res.json();
}

export async function logoutClient() {
  const res = await fetch(`${API_BASE}/clients/logout`, {
    method: 'POST',
    credentials: 'include',
  });
  return res.json();
}

export async function getClientDashboard(id: number) {
  const res = await fetch(`${API_BASE}/clients/${id}/dashboard`, {
    method: 'GET',
    credentials: 'include',
  });
  return res.json();
}

export async function updateClientDashboard(id: number, data: any) {
  const res = await fetch(`${API_BASE}/clients/${id}/dashboard`, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
    credentials: 'include',
  });
  return res.json();
} 