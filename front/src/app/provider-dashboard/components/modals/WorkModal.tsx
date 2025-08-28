import { useState } from "react";
import {
  Column,
  Input,
  Textarea,
  Button,
  Flex,
  Card,
  Heading,
} from "@/once-ui/components";

interface WorkModalProps {
  show: boolean;
  onClose: () => void;
  onCreate: (workData: any) => Promise<void>;
}

export function WorkModal({ show, onClose, onCreate }: WorkModalProps) {
  const [workForm, setWorkForm] = useState({
    company: "",
    title: "",
    description: "",
    startDate: "",
    endDate: "",
  });

  const handleSubmit = async () => {
    try {
      await onCreate(workForm);
      setWorkForm({
        company: "",
        title: "",
        description: "",
        startDate: "",
        endDate: "",
      });
      onClose();
    } catch (error) {
      console.error("Error creating work:", error);
    }
  };

  if (!show) return null;

  return (
    <div
      style={{
        position: "fixed",
        top: 0,
        left: 0,
        right: 0,
        bottom: 0,
        backgroundColor: "rgba(0, 0, 0, 0.5)",
        display: "flex",
        alignItems: "center",
        justifyContent: "center",
        zIndex: 1000,
      }}
    >
      <Card
        padding="24"
        style={{
          maxWidth: "500px",
          width: "90%",
          maxHeight: "80vh",
          overflow: "auto",
        }}
      >
        <Column gap="m">
          <Heading variant="heading-strong-m">Ajouter une réalisation</Heading>

          <Input
            id="work-company"
            label="Entreprise"
            value={workForm.company}
            onChange={(e) =>
              setWorkForm({ ...workForm, company: e.target.value })
            }
          />
          <Input
            id="work-title"
            label="Titre du poste"
            value={workForm.title}
            onChange={(e) =>
              setWorkForm({ ...workForm, title: e.target.value })
            }
          />
          <Textarea
            id="work-description"
            label="Description"
            value={workForm.description}
            onChange={(e) =>
              setWorkForm({ ...workForm, description: e.target.value })
            }
          />
          <Flex gap="m">
            <Input
              id="work-start-date"
              label="Date de début"
              type="date"
              value={workForm.startDate}
              onChange={(e) =>
                setWorkForm({ ...workForm, startDate: e.target.value })
              }
            />
            <Input
              id="work-end-date"
              label="Date de fin"
              type="date"
              value={workForm.endDate || ""}
              onChange={(e) =>
                setWorkForm({ ...workForm, endDate: e.target.value })
              }
            />
          </Flex>

          <Flex gap="m" horizontal="end">
            <Button variant="secondary" onClick={onClose}>
              Annuler
            </Button>
            <Button onClick={handleSubmit}>Créer</Button>
          </Flex>
        </Column>
      </Card>
    </div>
  );
}
