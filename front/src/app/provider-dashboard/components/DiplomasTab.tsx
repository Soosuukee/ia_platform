import { useState } from "react";
import { Column, Text, Button, Flex, Grid, Card } from "@/once-ui/components";
import { Provider } from "@/Interface";
import { DiplomaModal } from "./modals/DiplomaModal";

interface DiplomasTabProps {
  provider: Provider;
  onCreateDiploma: (diplomaData: any) => Promise<void>;
  onDeleteDiploma: (diplomaId: number) => Promise<void>;
}

export function DiplomasTab({
  provider,
  onCreateDiploma,
  onDeleteDiploma,
}: DiplomasTabProps) {
  const [showModal, setShowModal] = useState(false);

  return (
    <Column gap="m">
      <Flex horizontal="space-between" vertical="center">
        <Text variant="heading-strong-s">Mes Diplômes</Text>
        <Button onClick={() => setShowModal(true)}>Ajouter un diplôme</Button>
      </Flex>
      <Grid columns={1} gap="m">
        {provider.diplomas?.map((diploma) => (
          <Card
            key={diploma.id}
            padding="16"
            background="neutral-strong"
            radius="m"
          >
            <Column gap="s">
              <Flex horizontal="space-between" vertical="start">
                <Text variant="heading-strong-s">{diploma.title}</Text>
                <Button
                  variant="secondary"
                  size="s"
                  onClick={() => onDeleteDiploma(diploma.id)}
                >
                  ×
                </Button>
              </Flex>
              <Text variant="body-default-m">{diploma.institution}</Text>
              {diploma.description && (
                <Text variant="body-default-s">{diploma.description}</Text>
              )}
              <Text variant="label-default-s">
                {diploma.startDate} - {diploma.endDate || "En cours"}
              </Text>
            </Column>
          </Card>
        )) || []}
      </Grid>
      {(!provider.diplomas || provider.diplomas.length === 0) && (
        <Text variant="body-default-m" onBackground="neutral-weak">
          Aucun diplôme ajouté. Ajoutez vos formations pour renforcer votre
          profil !
        </Text>
      )}

      <DiplomaModal
        show={showModal}
        onClose={() => setShowModal(false)}
        onCreate={onCreateDiploma}
      />
    </Column>
  );
}
