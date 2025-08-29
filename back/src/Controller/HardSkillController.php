<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Controller;

use Soosuuke\IaPlatform\Repository\HardSkillRepository;
use Soosuuke\IaPlatform\Entity\HardSkill;

class HardSkillController
{
    private HardSkillRepository $hardSkillRepository;

    public function __construct()
    {
        $this->hardSkillRepository = new HardSkillRepository();
    }

    // GET /hard-skills
    public function getAllHardSkills(): array
    {
        return $this->hardSkillRepository->findAll();
    }

    // GET /hard-skills/{id}
    public function getHardSkillById(int $id): ?HardSkill
    {
        return $this->hardSkillRepository->findById($id);
    }

    // POST /hard-skills
    public function createHardSkill(array $data): HardSkill
    {
        $skill = new HardSkill($data['title']);
        $this->hardSkillRepository->save($skill);
        return $skill;
    }

    // PUT /hard-skills/{id}
    public function updateHardSkill(int $id, array $data): ?HardSkill
    {
        $skill = $this->hardSkillRepository->findById($id);
        if (!$skill) {
            return null;
        }

        $skill = new HardSkill($data['title'] ?? $skill->getTitle());
        $this->hardSkillRepository->update($skill);
        return $skill;
    }

    // DELETE /hard-skills/{id}
    public function deleteHardSkill(int $id): bool
    {
        $skill = $this->hardSkillRepository->findById($id);
        if (!$skill) {
            return false;
        }

        $this->hardSkillRepository->delete($id);
        return true;
    }
}
