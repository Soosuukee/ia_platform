"use client";

import { useState } from "react";
import { Column, Heading, Text, Button, Flex } from "@/once-ui/components";

// Import des composants modulaires
import { DashboardStats } from "./components/DashboardStats";
import { TabNavigation } from "./components/TabNavigation";
import { ServiceTab } from "./components/ServiceTab";
import { SkillsTab } from "./components/SkillsTab";
import { SlotsTab } from "./components/SlotsTab";
import { DiplomasTab } from "./components/DiplomasTab";
import { WorksTab } from "./components/WorksTab";
import { RequestsTab } from "./components/RequestsTab";

// Import des hooks
import { useDashboardData } from "./hooks/useDashboardData";
import { useServiceActions } from "./hooks/useServiceActions";

// Import des services
import { assignSkills, removeSkill } from "@/services/providerSkillService";
import { createSlot, deleteSlot } from "@/services/availabilitySlotService";
import {
  createDiploma,
  deleteDiploma,
} from "@/services/providerDiplomaService";
import {
  createCompletedWork,
  deleteCompletedWork,
} from "@/services/completedWorkService";

export default function ProviderDashboard() {
  const {
    userId,
    dashboardData,
    allSkills,
    loading,
    error,
    loadDashboardData,
  } = useDashboardData();
  const { createService, deleteService } = useServiceActions();
  const [activeTab, setActiveTab] = useState("services");

  // Handlers pour les actions
  const handleCreateService = async (serviceData: any) => {
    if (!userId) return;
    await createService(userId, serviceData, () => loadDashboardData(userId));
  };

  const handleDeleteService = async (serviceId: number) => {
    if (!userId) return;
    await deleteService(serviceId, () => loadDashboardData(userId));
  };

  const handleAssignSkills = async (skillIds: number[]) => {
    if (!userId) return;
    try {
      await assignSkills(userId, skillIds);
      await loadDashboardData(userId);
    } catch (err) {
      console.error("Skill assignment error:", err);
      throw err;
    }
  };

  const handleRemoveSkill = async (skillId: number) => {
    if (
      !userId ||
      !confirm("Êtes-vous sûr de vouloir retirer cette compétence ?")
    )
      return;
    try {
      await removeSkill(userId, skillId);
      await loadDashboardData(userId);
    } catch (err) {
      console.error("Skill removal error:", err);
    }
  };

  const handleCreateSlot = async (slotData: any) => {
    if (!userId) return;
    try {
      await createSlot(userId, slotData);
      await loadDashboardData(userId);
    } catch (err) {
      console.error("Slot creation error:", err);
      throw err;
    }
  };

  const handleDeleteSlot = async (slotId: number) => {
    if (!userId || !confirm("Êtes-vous sûr de vouloir supprimer ce créneau ?"))
      return;
    try {
      await deleteSlot(slotId);
      await loadDashboardData(userId);
    } catch (err) {
      console.error("Slot deletion error:", err);
    }
  };

  const handleCreateDiploma = async (diplomaData: any) => {
    if (!userId) return;
    try {
      await createDiploma(diplomaData);
      await loadDashboardData(userId);
    } catch (err) {
      console.error("Diploma creation error:", err);
      throw err;
    }
  };

  const handleDeleteDiploma = async (diplomaId: number) => {
    if (!userId || !confirm("Êtes-vous sûr de vouloir supprimer ce diplôme ?"))
      return;
    try {
      await deleteDiploma(diplomaId);
      await loadDashboardData(userId);
    } catch (err) {
      console.error("Diploma deletion error:", err);
    }
  };

  const handleCreateWork = async (workData: any) => {
    if (!userId) return;
    try {
      await createCompletedWork(workData);
      await loadDashboardData(userId);
    } catch (err) {
      console.error("Work creation error:", err);
      throw err;
    }
  };

  const handleDeleteWork = async (workId: number) => {
    if (
      !userId ||
      !confirm("Êtes-vous sûr de vouloir supprimer cette réalisation ?")
    )
      return;
    try {
      await deleteCompletedWork(workId);
      await loadDashboardData(userId);
    } catch (err) {
      console.error("Work deletion error:", err);
    }
  };

  // États de chargement et d'erreur
  if (loading) {
    return (
      <Column maxWidth="l" horizontal="center" gap="xl" paddingY="xl">
        <Text>Chargement du dashboard...</Text>
      </Column>
    );
  }

  if (error) {
    return (
      <Column maxWidth="l" horizontal="center" gap="xl" paddingY="xl">
        <Text variant="heading-strong-s" style={{ color: "red" }}>
          Erreur : {error}
        </Text>
        <Button onClick={() => userId && loadDashboardData(userId)}>
          Réessayer
        </Button>
      </Column>
    );
  }

  if (!dashboardData?.provider) {
    return (
      <Column maxWidth="l" horizontal="center" gap="xl" paddingY="xl">
        <Text>Aucune donnée disponible</Text>
        <Button onClick={() => userId && loadDashboardData(userId)}>
          Charger les données
        </Button>
      </Column>
    );
  }

  const { provider } = dashboardData;

  // Rendu du contenu des onglets
  const renderTabContent = () => {
    switch (activeTab) {
      case "services":
        return (
          <ServiceTab
            provider={provider}
            onCreateService={handleCreateService}
            onDeleteService={handleDeleteService}
          />
        );
      case "skills":
        return (
          <SkillsTab
            provider={provider}
            allSkills={allSkills}
            onAssignSkills={handleAssignSkills}
            onRemoveSkill={handleRemoveSkill}
          />
        );
      case "slots":
        return (
          <SlotsTab
            provider={provider}
            onCreateSlot={handleCreateSlot}
            onDeleteSlot={handleDeleteSlot}
          />
        );
      case "diplomas":
        return (
          <DiplomasTab
            provider={provider}
            onCreateDiploma={handleCreateDiploma}
            onDeleteDiploma={handleDeleteDiploma}
          />
        );
      case "works":
        return (
          <WorksTab
            provider={provider}
            onCreateWork={handleCreateWork}
            onDeleteWork={handleDeleteWork}
          />
        );
      case "requests":
        return <RequestsTab provider={provider} />;
      default:
        return null;
    }
  };

  return (
    <Column maxWidth="l" horizontal="center" gap="xl" paddingY="xl">
      {/* En-tête */}
      <Column gap="s" horizontal="center">
        <Heading variant="display-strong-m">Tableau de bord Provider</Heading>
        <Text variant="body-default-m" onBackground="neutral-weak">
          Bonjour {provider.firstName} {provider.lastName} ! Gérez vos services
          et données.
        </Text>
      </Column>

      {/* Statistiques */}
      <DashboardStats provider={provider} />

      {/* Navigation des onglets */}
      <TabNavigation activeTab={activeTab} onTabChange={setActiveTab} />

      {/* Contenu des onglets */}
      {renderTabContent()}
    </Column>
  );
}
