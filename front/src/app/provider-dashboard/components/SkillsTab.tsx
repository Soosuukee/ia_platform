import { useState } from "react";
import { Column, Text, Button, Flex, Tag } from "@/once-ui/components";
import { Provider, Skill } from "@/Interface";
import { SkillModal } from "./modals/SkillModal";

interface SkillsTabProps {
  provider: Provider;
  allSkills: Skill[];
  onAssignSkills: (skillIds: number[]) => Promise<void>;
  onRemoveSkill: (skillId: number) => Promise<void>;
}

export function SkillsTab({
  provider,
  allSkills,
  onAssignSkills,
  onRemoveSkill,
}: SkillsTabProps) {
  const [showModal, setShowModal] = useState(false);

  return (
    <Column gap="m">
      <Flex horizontal="space-between" vertical="center">
        <Text variant="heading-strong-s">Mes Compétences</Text>
        <Button onClick={() => setShowModal(true)}>
          Ajouter des compétences
        </Button>
      </Flex>
      <Flex gap="s" wrap>
        {provider.skills?.map((skill) => (
          <Flex key={skill.id} gap="s" vertical="center">
            <Tag>{skill.name}</Tag>
            <Button
              variant="secondary"
              size="s"
              onClick={() => onRemoveSkill(skill.id)}
            >
              ×
            </Button>
          </Flex>
        )) || []}
      </Flex>
      {(!provider.skills || provider.skills.length === 0) && (
        <Text variant="body-default-m" onBackground="neutral-weak">
          Aucune compétence ajoutée. Ajoutez vos compétences pour être mieux
          trouvé !
        </Text>
      )}

      <SkillModal
        show={showModal}
        onClose={() => setShowModal(false)}
        allSkills={allSkills}
        currentSkills={provider.skills || []}
        onAssign={onAssignSkills}
      />
    </Column>
  );
}
