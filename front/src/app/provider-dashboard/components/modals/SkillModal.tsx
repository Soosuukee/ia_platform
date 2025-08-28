import { useState } from "react";
import {
  Column,
  Text,
  Button,
  Flex,
  Card,
  Heading,
} from "@/once-ui/components";
import { Skill } from "@/Interface";

interface SkillModalProps {
  show: boolean;
  onClose: () => void;
  allSkills: Skill[];
  currentSkills: Skill[];
  onAssign: (skillIds: number[]) => Promise<void>;
}

export function SkillModal({
  show,
  onClose,
  allSkills,
  currentSkills,
  onAssign,
}: SkillModalProps) {
  const [selectedSkills, setSelectedSkills] = useState<number[]>([]);

  const handleSubmit = async () => {
    try {
      await onAssign(selectedSkills);
      setSelectedSkills([]);
      onClose();
    } catch (error) {
      console.error("Error assigning skills:", error);
    }
  };

  const currentSkillIds = currentSkills.map((skill) => skill.id);
  const availableSkills = allSkills.filter(
    (skill) => !currentSkillIds.includes(skill.id)
  );

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
          <Heading variant="heading-strong-m">Ajouter des compétences</Heading>

          <Column gap="s">
            {availableSkills.map((skill) => (
              <label
                key={skill.id}
                style={{ display: "flex", alignItems: "center", gap: "8px" }}
              >
                <input
                  type="checkbox"
                  value={skill.id}
                  checked={selectedSkills.includes(skill.id)}
                  onChange={(e) => {
                    const skillId = parseInt(e.target.value);
                    if (e.target.checked) {
                      setSelectedSkills((prev) => [...prev, skillId]);
                    } else {
                      setSelectedSkills((prev) =>
                        prev.filter((id) => id !== skillId)
                      );
                    }
                  }}
                />
                <Text>{skill.name}</Text>
              </label>
            ))}
          </Column>

          {availableSkills.length === 0 && (
            <Text variant="body-default-m" onBackground="neutral-weak">
              Toutes les compétences disponibles ont déjà été ajoutées.
            </Text>
          )}

          <Flex gap="m" horizontal="end">
            <Button variant="secondary" onClick={onClose}>
              Annuler
            </Button>
            <Button
              onClick={handleSubmit}
              disabled={selectedSkills.length === 0}
            >
              Ajouter
            </Button>
          </Flex>
        </Column>
      </Card>
    </div>
  );
}
