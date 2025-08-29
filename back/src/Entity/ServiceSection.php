<?php

declare(strict_types=1);

namespace Soosuuke\IaPlatform\Entity;

class ServiceSection
{
    private int $id;
    private int $serviceId;
    private string $title;

    public function __construct(int $serviceId, string $title)
    {
        $this->serviceId = $serviceId;
        $this->title = trim($title);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getServiceId(): int
    {
        return $this->serviceId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'serviceId' => $this->serviceId,
        ];
    }
}
