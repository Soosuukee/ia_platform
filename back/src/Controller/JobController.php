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
        $jobs = $this->jobRepository->findAll();
        return array_map(fn(Job $j) => $j->toArray(), $jobs);
    }

    // GET /jobs/{id}
    public function getJobById(int $id): ?array
    {
        $job = $this->jobRepository->findById($id);
        return $job ? $job->toArray() : null;
    }

    // GET /jobs/slug/{slug}
    public function getJobBySlug(string $slug): ?array
    {
        $job = $this->jobRepository->findBySlug($slug);
        return $job ? $job->toArray() : null;
    }

    // POST /jobs
    public function createJob(array $data): Job
    {
        $job = new Job($data['title'], $data['slug'] ?? null);
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

        $job = new Job($data['title'] ?? $job->getTitle(), $data['slug'] ?? $job->getSlug());
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
