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

interface ServiceModalProps {
  show: boolean;
  onClose: () => void;
  onCreate: (serviceData: any) => Promise<void>;
}

export function ServiceModal({ show, onClose, onCreate }: ServiceModalProps) {
  const [serviceForm, setServiceForm] = useState({
    title: "",
    description: "",
    minPrice: "",
    maxPrice: "",
    duration: "",
  });

  const handleSubmit = async () => {
    try {
      const serviceData = {
        title: serviceForm.title,
        description: serviceForm.description,
        minPrice: serviceForm.minPrice
          ? parseInt(serviceForm.minPrice)
          : undefined,
        maxPrice: serviceForm.maxPrice
          ? parseInt(serviceForm.maxPrice)
          : undefined,
        duration: serviceForm.duration,
      };

      await onCreate(serviceData);
      setServiceForm({
        title: "",
        description: "",
        minPrice: "",
        maxPrice: "",
        duration: "",
      });
      onClose();
    } catch (error) {
      console.error("Error creating service:", error);
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
          <Heading variant="heading-strong-m">Ajouter un service</Heading>

          <Input
            id="service-title"
            label="Titre"
            value={serviceForm.title}
            onChange={(e) =>
              setServiceForm({ ...serviceForm, title: e.target.value })
            }
          />
          <Textarea
            id="service-description"
            label="Description"
            value={serviceForm.description}
            onChange={(e) =>
              setServiceForm({ ...serviceForm, description: e.target.value })
            }
          />
          <Flex gap="m">
            <Input
              id="service-min-price"
              label="Prix minimum (€)"
              type="number"
              value={serviceForm.minPrice}
              onChange={(e) =>
                setServiceForm({ ...serviceForm, minPrice: e.target.value })
              }
            />
            <Input
              id="service-max-price"
              label="Prix maximum (€)"
              type="number"
              value={serviceForm.maxPrice}
              onChange={(e) =>
                setServiceForm({ ...serviceForm, maxPrice: e.target.value })
              }
            />
          </Flex>
          <Input
            id="service-duration"
            label="Durée"
            value={serviceForm.duration}
            onChange={(e) =>
              setServiceForm({ ...serviceForm, duration: e.target.value })
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
