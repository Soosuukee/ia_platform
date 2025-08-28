import { useState } from "react";
import {
  Column,
  Text,
  Button,
  Flex,
  Grid,
  Card,
  Tag,
} from "@/once-ui/components";
import { Provider } from "@/Interface";
import { SlotModal } from "./modals/SlotModal";

interface SlotsTabProps {
  provider: Provider;
  onCreateSlot: (slotData: any) => Promise<void>;
  onDeleteSlot: (slotId: number) => Promise<void>;
}

export function SlotsTab({
  provider,
  onCreateSlot,
  onDeleteSlot,
}: SlotsTabProps) {
  const [showModal, setShowModal] = useState(false);

  return (
    <Column gap="m">
      <Flex horizontal="space-between" vertical="center">
        <Text variant="heading-strong-s">Mes Créneaux</Text>
        <Button onClick={() => setShowModal(true)}>Ajouter un créneau</Button>
      </Flex>
      <Grid columns={2} gap="m">
        {provider.availabilitySlots?.map((slot) => (
          <Card
            key={slot.id}
            padding="16"
            background="neutral-strong"
            radius="m"
          >
            <Column gap="s">
              <Flex horizontal="space-between" vertical="start">
                <Text variant="heading-strong-s">
                  {new Date(slot.startTime).toLocaleDateString()}
                </Text>
                <Button
                  variant="secondary"
                  size="s"
                  onClick={() => onDeleteSlot(slot.id)}
                >
                  ×
                </Button>
              </Flex>
              <Text variant="body-default-s">
                {new Date(slot.startTime).toLocaleTimeString()} -{" "}
                {new Date(slot.endTime).toLocaleTimeString()}
              </Text>
              <Tag>{slot.isBooked ? "Réservé" : "Disponible"}</Tag>
            </Column>
          </Card>
        )) || []}
      </Grid>
      {(!provider.availabilitySlots ||
        provider.availabilitySlots.length === 0) && (
        <Text variant="body-default-m" onBackground="neutral-weak">
          Aucun créneau de disponibilité. Ajoutez vos créneaux pour recevoir des
          réservations !
        </Text>
      )}

      <SlotModal
        show={showModal}
        onClose={() => setShowModal(false)}
        onCreate={onCreateSlot}
      />
    </Column>
  );
}
