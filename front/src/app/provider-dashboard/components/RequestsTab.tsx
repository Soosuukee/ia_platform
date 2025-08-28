import { Column, Text, Grid, Card, Tag, Flex } from "@/once-ui/components";
import { Provider } from "@/Interface";

interface RequestsTabProps {
  provider: Provider;
}

export function RequestsTab({ provider }: RequestsTabProps) {
  return (
    <Column gap="m">
      <Text variant="heading-strong-s">Demandes des clients</Text>
      <Grid columns={1} gap="m">
        {provider.requests?.map((request) => (
          <Card
            key={request.id}
            padding="16"
            background="neutral-strong"
            radius="m"
          >
            <Column gap="s">
              <Text variant="heading-strong-s">{request.title}</Text>
              <Text variant="body-default-s">{request.description}</Text>
              <Flex horizontal="space-between" vertical="center">
                <Tag>{request.status}</Tag>
                <Text variant="label-default-s">
                  {new Date(request.createdAt).toLocaleDateString()}
                </Text>
              </Flex>
            </Column>
          </Card>
        )) || []}
      </Grid>
      {(!provider.requests || provider.requests.length === 0) && (
        <Text variant="body-default-m" onBackground="neutral-weak">
          Aucune demande reçue pour le moment.
        </Text>
      )}
    </Column>
  );
}
