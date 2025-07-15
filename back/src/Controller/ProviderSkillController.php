<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Controller;

use Soosuuke\IaPlatform\Repository\ProviderSkillRepository;
use Soosuuke\IaPlatform\Repository\SkillRepository;
use Soosuuke\IaPlatform\Entity\ProviderSkill;

class ProviderSkillController
{
    private ProviderSkillRepository $repo;
    private SkillRepository $skillRepo;

    public function __construct(
        ProviderSkillRepository $repo,
        SkillRepository $skillRepo
    ) {
        $this->repo = $repo;
        $this->skillRepo = $skillRepo;
    }

    // GET /providers/{providerId}/skills
    public function list(int $providerId): void
    {
        session_start();
        $sessionProviderId = $_SESSION['provider_id'] ?? null;
        if ($providerId !== $sessionProviderId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $skills = $this->repo->findAllSkillsByProviderId($providerId);
        $result = array_map(fn($skill) => [
            'id' => $skill->getId(),
            'name' => $skill->getName()
        ], $skills);

        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    // POST /providers/{providerId}/skills
    public function assign(int $providerId): void
    {
        session_start();
        $sessionProviderId = $_SESSION['provider_id'] ?? null;
        if ($providerId !== $sessionProviderId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        // CSRF protection
        $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $csrfToken)) {
            http_response_code(403);
            echo json_encode(['error' => 'Invalid CSRF token']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['skill_ids']) || !is_array($data['skill_ids']) || empty($data['skill_ids'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid or empty skill_ids payload']);
            exit;
        }

        // Validate skill IDs
        foreach ($data['skill_ids'] as $skillId) {
            if (!is_int($skillId) || $skillId <= 0) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid skill ID: ' . $skillId]);
                exit;
            }
            // Ensure skill exists
            if (!$this->skillRepo->findById($skillId)) {
                http_response_code(400);
                echo json_encode(['error' => 'Skill ID not found: ' . $skillId]);
                exit;
            }
        }

        foreach ($data['skill_ids'] as $skillId) {
            $this->repo->save(new ProviderSkill($providerId, (int) $skillId));
        }

        $this->logAction("Provider {$providerId} assigned skills: " . implode(', ', $data['skill_ids']));

        http_response_code(201);
        echo json_encode(['message' => 'Skills assigned successfully']);
        exit;
    }

    // DELETE /providers/{providerId}/skills/{skillId}
    public function remove(int $providerId, int $skillId): void
    {
        session_start();
        $sessionProviderId = $_SESSION['provider_id'] ?? null;
        if ($providerId !== $sessionProviderId) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        // CSRF protection
        $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $csrfToken)) {
            http_response_code(403);
            echo json_encode(['error' => 'Invalid CSRF token']);
            exit;
        }

        // Validate skill ID
        if (!$this->skillRepo->findById($skillId)) {
            http_response_code(400);
            echo json_encode(['error' => 'Skill ID not found: ' . $skillId]);
            exit;
        }

        if (!$this->repo->deleteByProviderAndSkillId($providerId, $skillId)) {
            http_response_code(404);
            echo json_encode(['error' => 'Skill not assigned to provider']);
            exit;
        }

        $this->logAction("Provider {$providerId} removed skill {$skillId}");

        http_response_code(200);
        echo json_encode(['message' => 'Skill removed successfully']);
        exit;
    }

    private function logAction(string $message): void
    {
        $logMessage = sprintf("[%s] %s\n", date('Y-m-d H:i:s'), $message);
        file_put_contents(__DIR__ . '/../../logs/provider_actions.log', $logMessage, FILE_APPEND);
    }
}
