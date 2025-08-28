<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Controller;

use Soosuuke\IaPlatform\Repository\JobRepository;
use Soosuuke\IaPlatform\Entity\Job;

class JobController
{
    private JobRepository $jobRepository;

    public function __construct()
    {
        $this->jobRepository = new JobRepository();
    }

    // GET /jobs
    public function getAllJobs(): array
    {
        return $this->jobRepository->findAll();
    }

    // GET /jobs/{id}
    public function getJobById(int $id): ?Job
    {
        return $this->jobRepository->findById($id);
    }

    // POST /jobs
    public function createJob(array $data): Job
    {
        $job = new Job($data['title']);
        $this->jobRepository->save($job);
        return $job;
    }

    // PUT /jobs/{id}
    public function updateJob(int $id, array $data): ?Job
    {
        $job = $this->jobRepository->findById($id);
        if (!$job) {
            return null;
        }

        $job = new Job($data['title'] ?? $job->getTitle());
        $this->jobRepository->update($job);
        return $job;
    }

    // DELETE /jobs/{id}
    public function deleteJob(int $id): bool
    {
        $job = $this->jobRepository->findById($id);
        if (!$job) {
            return false;
        }

        $this->jobRepository->delete($id);
        return true;
    }
}
