import { useState } from "react";
import { createProviderService, deleteProvidedService } from "@/services/providedServiceService";

export function useServiceActions() {
  const [error, setError] = useState<string | null>(null);

  const createService = async (userId: number, serviceData: any, onSuccess?: () => void) => {
    try {
      setError(null);
      await createProviderService(userId, serviceData);
      if (onSuccess) onSuccess();
    } catch (err) {
      console.error("Service creation error:", err);
      setError(
        err instanceof Error
          ? err.message
          : "Erreur lors de la création du service"
      );
      throw err;
    }
  };

  const deleteService = async (serviceId: number, onSuccess?: () => void) => {
    if (!confirm("Êtes-vous sûr de vouloir supprimer ce service ?")) return;

    try {
      setError(null);
      await deleteProvidedService(serviceId);
      if (onSuccess) onSuccess();
    } catch (err) {
      console.error("Service deletion error:", err);
      setError(
        err instanceof Error ? err.message : "Erreur lors de la suppression"
      );
      throw err;
    }
  };

  return {
    createService,
    deleteService,
    error,
  };
} 