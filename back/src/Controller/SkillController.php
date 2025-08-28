<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Controller;

use Soosuuke\IaPlatform\Repository\SoftSkillRepository;
use Soosuuke\IaPlatform\Repository\HardSkillRepository;
use Soosuuke\IaPlatform\Entity\SoftSkill;
use Soosuuke\IaPlatform\Entity\HardSkill;

class SkillController
{
    private SoftSkillRepository $softSkillRepository;
    private HardSkillRepository $hardSkillRepository;

    public function __construct()
    {
        $this->softSkillRepository = new SoftSkillRepository();
        $this->hardSkillRepository = new HardSkillRepository();
    }

    // === SOFT SKILLS ===

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

    // === HARD SKILLS ===

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
