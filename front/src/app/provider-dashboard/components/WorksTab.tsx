import { useState } from "react";
import { Column, Text, Button, Flex, Grid, Card } from "@/once-ui/components";
import { Provider } from "@/Interface";
import { WorkModal } from "./modals/WorkModal";

interface WorksTabProps {
  provider: Provider;
  onCreateWork: (workData: any) => Promise<void>;
  onDeleteWork: (workId: number) => Promise<void>;
}

export function WorksTab({
  provider,
  onCreateWork,
  onDeleteWork,
}: WorksTabProps) {
  const [showModal, setShowModal] = useState(false);

  return (
    <Column gap="m">
      <Flex horizontal="space-between" vertical="center">
        <Text variant="heading-strong-s">Mes Réalisations</Text>
        <Button onClick={() => setShowModal(true)}>
          Ajouter une réalisation
        </Button>
      </Flex>
      <Grid columns={1} gap="m">
        {provider.completedWorks?.map((work) => (
          <Card
            key={work.id}
            padding="16"
            background="neutral-strong"
            radius="m"
          >
            <Column gap="s">
              <Flex horizontal="space-between" vertical="start">
                <Text variant="heading-strong-s">{work.title}</Text>
                <Button
                  variant="secondary"
                  size="s"
                  onClick={() => onDeleteWork(work.id)}
                >
                  ×
                </Button>
              </Flex>
              <Text variant="body-default-m">{work.company}</Text>
              <Text variant="body-default-s">{work.description}</Text>
              <Text variant="label-default-s">
                {work.startDate} - {work.endDate || "En cours"}
              </Text>
            </Column>
          </Card>
        )) || []}
      </Grid>
      {(!provider.completedWorks || provider.completedWorks.length === 0) && (
        <Text variant="body-default-m" onBackground="neutral-weak">
          Aucune réalisation ajoutée. Montrez vos projets passés pour attirer
          des clients !
        </Text>
      )}

      <WorkModal
        show={showModal}
        onClose={() => setShowModal(false)}
        onCreate={onCreateWork}
      />
    </Column>
  );
}
