// Review Service: Handles API calls for reviews

const API_BASE = process.env.NEXT_PUBLIC_API_BASE || 'http://localhost:8080/api/v1';

export async function getProviderReviews(providerId: number) {
  const res = await fetch(`${API_BASE}/providers/${providerId}/reviews`, {
    method: 'GET',
    credentials: 'include',
  });
  return res.json();
}

export async function getReview(id: number) {
  const res = await fetch(`${API_BASE}/reviews/${id}`, {
    method: 'GET',
    credentials: 'include',
  });
  return res.json();
}

export async function createReview(data: any) {
  const res = await fetch(`${API_BASE}/reviews`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
    credentials: 'include',
  });
  return res.json();
}

export async function patchReview(id: number, data: any) {
  const res = await fetch(`${API_BASE}/reviews/${id}`, {
    method: 'PATCH',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
    credentials: 'include',
  });
  return res.json();
}

export async function deleteReview(id: number) {
  const res = await fetch(`${API_BASE}/reviews/${id}`, {
    method: 'DELETE',
    credentials: 'include',
  });
  return res.json();
} 