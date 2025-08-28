import { Card, Column, Text, Grid } from "@/once-ui/components";
import { Provider } from "@/Interface";

interface DashboardStatsProps {
  provider: Provider;
}

export function DashboardStats({ provider }: DashboardStatsProps) {
  return (
    <Grid columns={4} gap="m">
      <Card padding="16" background="neutral-strong" radius="m">
        <Column gap="s">
          <Text variant="label-strong-s">Services</Text>
          <Text variant="display-strong-m">
            {provider.services?.length || 0}
          </Text>
        </Column>
      </Card>
      <Card padding="16" background="neutral-strong" radius="m">
        <Column gap="s">
          <Text variant="label-strong-s">Créneaux</Text>
          <Text variant="display-strong-m">
            {provider.availabilitySlots?.length || 0}
          </Text>
        </Column>
      </Card>
      <Card padding="16" background="neutral-strong" radius="m">
        <Column gap="s">
          <Text variant="label-strong-s">Compétences</Text>
          <Text variant="display-strong-m">{provider.skills?.length || 0}</Text>
        </Column>
      </Card>
      <Card padding="16" background="neutral-strong" radius="m">
        <Column gap="s">
          <Text variant="label-strong-s">Demandes</Text>
          <Text variant="display-strong-m">
            {provider.requests?.length || 0}
          </Text>
        </Column>
      </Card>
    </Grid>
  );
}
