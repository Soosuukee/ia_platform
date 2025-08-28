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

interface DiplomaModalProps {
  show: boolean;
  onClose: () => void;
  onCreate: (diplomaData: any) => Promise<void>;
}

export function DiplomaModal({ show, onClose, onCreate }: DiplomaModalProps) {
  const [diplomaForm, setDiplomaForm] = useState({
    title: "",
    institution: "",
    description: "",
    startDate: "",
    endDate: "",
  });

  const handleSubmit = async () => {
    try {
      await onCreate(diplomaForm);
      setDiplomaForm({
        title: "",
        institution: "",
        description: "",
        startDate: "",
        endDate: "",
      });
      onClose();
    } catch (error) {
      console.error("Error creating diploma:", error);
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
          <Heading variant="heading-strong-m">Ajouter un diplôme</Heading>

          <Input
            id="diploma-title"
            label="Titre"
            value={diplomaForm.title}
            onChange={(e) =>
              setDiplomaForm({ ...diplomaForm, title: e.target.value })
            }
          />
          <Input
            id="diploma-institution"
            label="Institution"
            value={diplomaForm.institution}
            onChange={(e) =>
              setDiplomaForm({ ...diplomaForm, institution: e.target.value })
            }
          />
          <Textarea
            id="diploma-description"
            label="Description"
            value={diplomaForm.description || ""}
            onChange={(e) =>
              setDiplomaForm({ ...diplomaForm, description: e.target.value })
            }
          />
          <Flex gap="m">
            <Input
              id="diploma-start-date"
              label="Date de début"
              type="date"
              value={diplomaForm.startDate || ""}
              onChange={(e) =>
                setDiplomaForm({ ...diplomaForm, startDate: e.target.value })
              }
            />
            <Input
              id="diploma-end-date"
              label="Date de fin"
              type="date"
              value={diplomaForm.endDate || ""}
              onChange={(e) =>
                setDiplomaForm({ ...diplomaForm, endDate: e.target.value })
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
