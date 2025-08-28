<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

class ProviderJob
{
    private int $id;
    private int $providerId;
    private int $jobId;

    public function __construct(int $providerId, int $jobId)
    {
        $this->providerId = $providerId;
        $this->jobId = $jobId;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getProviderId(): int
    {
        return $this->providerId;
    }

    public function getJobId(): int
    {
        return $this->jobId;
    }
}
