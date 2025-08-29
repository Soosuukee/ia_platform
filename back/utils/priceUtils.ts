/**
 * Formate l'affichage des prix avec gestion des valeurs nulles
 */
export function formatPrice(minPrice: number | null, maxPrice: number | null): string {
  // Si les deux prix sont null, afficher "Sur devis"
  if (minPrice === null && maxPrice === null) {
    return "Sur devis";
  }
  
  // Si seul le prix minimum est défini
  if (minPrice !== null && maxPrice === null) {
    return `À partir de ${minPrice}€`;
  }
  
  // Si seul le prix maximum est défini
  if (minPrice === null && maxPrice !== null) {
    return `Jusqu'à ${maxPrice}€`;
  }
  
  // Si les deux prix sont définis
  if (minPrice !== null && maxPrice !== null) {
    // Si les prix sont identiques
    if (minPrice === maxPrice) {
      return `${minPrice}€`;
    }
    // Si les prix sont différents
    return `${minPrice}€ - ${maxPrice}€`;
  }
  
  // Fallback
  return "Sur devis";
}
