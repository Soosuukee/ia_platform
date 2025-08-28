import { useState } from "react";
import { Column, Text, Button, Flex, Grid, Card } from "@/once-ui/components";
import { Provider } from "@/Interface";
import { ServiceModal } from "./modals/ServiceModal";

interface ServiceTabProps {
  provider: Provider;
  onCreateService: (serviceData: any) => Promise<void>;
  onDeleteService: (serviceId: number) => Promise<void>;
}

export function ServiceTab({
  provider,
  onCreateService,
  onDeleteService,
}: ServiceTabProps) {
  const [showModal, setShowModal] = useState(false);

  return (
    <Column gap="m">
      <Flex horizontal="space-between" vertical="center">
        <Text variant="heading-strong-s">Mes Services</Text>
        <Button onClick={() => setShowModal(true)}>Ajouter un service</Button>
      </Flex>
      <Grid columns={2} gap="m">
        {provider.services?.map((service) => (
          <Card
            key={service.id}
            padding="16"
            background="neutral-strong"
            radius="m"
          >
            <Column gap="s">
              <Flex horizontal="space-between" vertical="start">
                <Text variant="heading-strong-s">{service.title}</Text>
                <Button
                  variant="secondary"
                  size="s"
                  onClick={() => onDeleteService(service.id)}
                >
                  ×
                </Button>
              </Flex>
              <Text variant="body-default-s">{service.description}</Text>
              <Flex gap="s" wrap>
                <Text variant="label-default-s">
                  {service.minPrice}€ - {service.maxPrice}€
                </Text>
                <Text variant="label-default-s">{service.duration}</Text>
              </Flex>
            </Column>
          </Card>
        )) || []}
      </Grid>
      {(!provider.services || provider.services.length === 0) && (
        <Text variant="body-default-m" onBackground="neutral-weak">
          Aucun service créé. Commencez par ajouter votre premier service !
        </Text>
      )}

      <ServiceModal
        show={showModal}
        onClose={() => setShowModal(false)}
        onCreate={onCreateService}
      />
    </Column>
  );
}
