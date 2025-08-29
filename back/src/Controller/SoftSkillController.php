<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Controller;

use Soosuuke\IaPlatform\Repository\SoftSkillRepository;
use Soosuuke\IaPlatform\Entity\SoftSkill;

class SoftSkillController
{
    private SoftSkillRepository $softSkillRepository;

    public function __construct()
    {
        $this->softSkillRepository = new SoftSkillRepository();
    }

    // GET /soft-skills
    public function getAllSoftSkills(): array
    {
        return $this->softSkillRepository->findAll();
    }

    // GET /soft-skills/{id}
    public function getSoftSkillById(int $id): ?SoftSkill
    {
        return $this->softSkillRepository->findById($id);
    }

    // POST /soft-skills
    public function createSoftSkill(array $data): SoftSkill
    {
        $skill = new SoftSkill($data['title']);
        $this->softSkillRepository->save($skill);
        return $skill;
    }

    // PUT /soft-skills/{id}
    public function updateSoftSkill(int $id, array $data): ?SoftSkill
    {
        $skill = $this->softSkillRepository->findById($id);
        if (!$skill) {
            return null;
        }

        $skill = new SoftSkill($data['title'] ?? $skill->getTitle());
        $this->softSkillRepository->update($skill);
        return $skill;
    }

    // DELETE /soft-skills/{id}
    public function deleteSoftSkill(int $id): bool
    {
        $skill = $this->softSkillRepository->findById($id);
        if (!$skill) {
            return false;
        }

        $this->softSkillRepository->delete($id);
        return true;
    }
}
