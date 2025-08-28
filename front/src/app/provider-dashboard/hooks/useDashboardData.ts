import { useState, useEffect } from "react";
import { useRouter } from "next/navigation";
import { DashboardData, Skill } from "@/Interface";
import { getAllSkills } from "@/services/providerSkillService";

export function useDashboardData() {
  const router = useRouter();
  const [userId, setUserId] = useState<number | null>(null);
  const [userType, setUserType] = useState<string | null>(null);
  const [dashboardData, setDashboardData] = useState<DashboardData | null>(null);
  const [allSkills, setAllSkills] = useState<Skill[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const loadDashboardData = async (providerId: number) => {
    try {
      setLoading(true);
      setError(null);

      const authTest = await fetch(
        `${
          process.env.NEXT_PUBLIC_API_BASE || "http://localhost:8080/api/v1"
        }/providers/${providerId}/dashboard`,
        {
          method: "GET",
          credentials: "include",
          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
          },
        }
      );

      if (!authTest.ok) {
        const errorText = await authTest.text();
        throw new Error(
          `Authentification échouée (${authTest.status}): ${errorText}`
        );
      }

      const data: DashboardData = await authTest.json();
      setDashboardData(data);

      // Charger toutes les compétences disponibles
      try {
        const skills = await getAllSkills();
        setAllSkills(skills);
      } catch (skillError) {
        console.warn("Could not load skills:", skillError);
        setAllSkills([]);
      }
    } catch (err) {
      console.error("Dashboard loading error:", err);
      setError(
        err instanceof Error
          ? err.message
          : "Erreur lors du chargement des données"
      );
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    // Vérifier l'authentification
    const storedUserType = localStorage.getItem("userType");
    const storedUserId = localStorage.getItem("userId");

    if (!storedUserType || storedUserType !== "provider" || !storedUserId) {
      router.push("/login");
      return;
    }

    setUserType(storedUserType);
    const id = parseInt(storedUserId);
    setUserId(id);
    loadDashboardData(id);
  }, [router]);

  return {
    userId,
    userType,
    dashboardData,
    allSkills,
    loading,
    error,
    loadDashboardData,
  };
} 