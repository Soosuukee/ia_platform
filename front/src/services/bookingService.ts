// Booking Service: Handles API calls for booking-related actions

const API_BASE = process.env.NEXT_PUBLIC_API_BASE || 'http://localhost:8080/api/v1';

export async function getBooking(id: number) {
  const res = await fetch(`${API_BASE}/bookings/${id}`, {
    method: 'GET',
    credentials: 'include',
  });
  return res.json();
}

export async function createBooking(data: any) {
  const res = await fetch(`${API_BASE}/bookings`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
    credentials: 'include',
  });
  return res.json();
}

export async function updateBooking(id: number, data: any) {
  const res = await fetch(`${API_BASE}/bookings/${id}`, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
    credentials: 'include',
  });
  return res.json();
}

export async function patchBooking(id: number, data: any) {
  const res = await fetch(`${API_BASE}/bookings/${id}`, {
    method: 'PATCH',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
    credentials: 'include',
  });
  return res.json();
}

export async function deleteBooking(id: number) {
  const res = await fetch(`${API_BASE}/bookings/${id}`, {
    method: 'DELETE',
    credentials: 'include',
  });
  return res.json();
} 