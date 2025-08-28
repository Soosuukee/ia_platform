// Skill Service: Handles API calls for global skills

const API_BASE = process.env.NEXT_PUBLIC_API_BASE || 'http://localhost:8080/api/v1';

interface Skill {
  id: number;
  name: string;
}

export async function getAllSkills(): Promise<Skill[]> {
  const res = await fetch(`${API_BASE}/skills`, {
    method: 'GET',
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    }
  });
  
  if (!res.ok) {
    throw new Error('Failed to fetch all skills');
  }
  
  return res.json();
}

export async function getSkill(id: number): Promise<Skill> {
  const res = await fetch(`${API_BASE}/skills/${id}`, {
    method: 'GET',
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    }
  });
  
  if (!res.ok) {
    throw new Error('Failed to fetch skill');
  }
  
  return res.json();
} 