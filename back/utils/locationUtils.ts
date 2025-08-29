import { Location } from '@/app/types/location';
import locations from '@/data/locations.json';

/**
 * Récupère toutes les locations
 */
export function getAllLocations(): Location[] {
  return locations as Location[];
}

/**
 * Trouve une location par son ID
 */
export function getLocationById(locationId: number): Location | undefined {
  return (locations as Location[]).find(location => location.locationId === locationId);
}

/**
 * Trouve une location par son nom
 */
export function getLocationByName(name: string): Location | undefined {
  return (locations as Location[]).find(location => 
    location.name.toLowerCase() === name.toLowerCase()
  );
}
