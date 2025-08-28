import { Button, Flex } from "@/once-ui/components";

interface TabNavigationProps {
  activeTab: string;
  onTabChange: (tab: string) => void;
}

export function TabNavigation({ activeTab, onTabChange }: TabNavigationProps) {
  const tabs = [
    { value: "services", label: "Services" },
    { value: "skills", label: "Compétences" },
    { value: "slots", label: "Disponibilités" },
    { value: "diplomas", label: "Diplômes" },
    { value: "works", label: "Réalisations" },
    { value: "requests", label: "Demandes" },
  ];

  return (
    <Flex gap="s" wrap>
      {tabs.map((tab) => (
        <Button
          key={tab.value}
          variant={activeTab === tab.value ? "primary" : "secondary"}
          onClick={() => onTabChange(tab.value)}
        >
          {tab.label}
        </Button>
      ))}
    </Flex>
  );
}
