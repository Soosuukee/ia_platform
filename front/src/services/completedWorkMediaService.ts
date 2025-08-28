// Completed Work Media Service: Handles API calls for completed work media

const API_BASE = process.env.NEXT_PUBLIC_API_BASE || 'http://localhost:8080/api/v1';

export async function getCompletedWorkMedia(workId: number) {
  const res = await fetch(`${API_BASE}/completed-works/${workId}/media`, {
    method: 'GET',
    credentials: 'include',
  });
  return res.json();
}

export async function createCompletedWorkMedia(data: any) {
  const res = await fetch(`${API_BASE}/completed-work-media`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
    credentials: 'include',
  });
  return res.json();
}

export async function patchCompletedWorkMedia(id: number, data: any) {
  const res = await fetch(`${API_BASE}/completed-work-media/${id}`, {
    method: 'PATCH',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
    credentials: 'include',
  });
  return res.json();
}

export async function deleteCompletedWorkMedia(id: number) {
  const res = await fetch(`${API_BASE}/completed-work-media/${id}`, {
    method: 'DELETE',
    credentials: 'include',
  });
  return res.json();
} 