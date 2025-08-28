import { useState } from "react";
import {
  Column,
  Input,
  Button,
  Flex,
  Card,
  Heading,
} from "@/once-ui/components";
import { CreateSlotData } from "@/Interface";

interface SlotModalProps {
  show: boolean;
  onClose: () => void;
  onCreate: (slotData: CreateSlotData) => Promise<void>;
}

export function SlotModal({ show, onClose, onCreate }: SlotModalProps) {
  const [slotForm, setSlotForm] = useState<CreateSlotData>({
    startTime: "",
    endTime: "",
  });

  const handleSubmit = async () => {
    try {
      await onCreate(slotForm);
      setSlotForm({ startTime: "", endTime: "" });
      onClose();
    } catch (error) {
      console.error("Error creating slot:", error);
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
          <Heading variant="heading-strong-m">Ajouter un créneau</Heading>

          <Input
            id="slot-start-time"
            label="Date et heure de début"
            type="datetime-local"
            value={slotForm.startTime}
            onChange={(e) =>
              setSlotForm({ ...slotForm, startTime: e.target.value })
            }
          />
          <Input
            id="slot-end-time"
            label="Date et heure de fin"
            type="datetime-local"
            value={slotForm.endTime}
            onChange={(e) =>
              setSlotForm({ ...slotForm, endTime: e.target.value })
            }
          />

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
