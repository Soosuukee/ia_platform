// Provider Skill Service: Handles API calls for provider skills

import { getAllSkills as fetchAllSkills } from './skillService';

const API_BASE = process.env.NEXT_PUBLIC_API_BASE || 'http://localhost:8080/api/v1';

interface Skill {
  id: number;
  name: string;
}

export async function getProviderSkills(providerId: number): Promise<Skill[]> {
  const res = await fetch(`${API_BASE}/providers/${providerId}/skills`, {
    method: 'GET',
    credentials: 'include',
  });
  
  if (!res.ok) {
    throw new Error('Failed to fetch skills');
  }
  
  return res.json();
}

export async function getAllSkills(): Promise<Skill[]> {
  return fetchAllSkills();
}

export async function assignSkills(providerId: number, skillIds: number[]): Promise<any> {
  const res = await fetch(`${API_BASE}/providers/${providerId}/skills`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ skill_ids: skillIds }),
    credentials: 'include',
  });
  
  if (!res.ok) {
    const error = await res.json();
    throw new Error(error.error || 'Failed to assign skills');
  }
  
  return res.json();
}

export async function removeSkill(providerId: number, skillId: number): Promise<any> {
  const res = await fetch(`${API_BASE}/providers/${providerId}/skills/${skillId}`, {
    method: 'DELETE',
    credentials: 'include',
  });
  
  if (!res.ok) {
    const error = await res.json();
    throw new Error(error.error || 'Failed to remove skill');
  }
  
  return res.json();
} 